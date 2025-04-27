<?php

namespace App\Actions\Element;

use App\Company;
use App\Enums\UserEntityType;
use App\Enums\UserRoleEnum;
use App\Exceptions\EntityAlreadyExistException;
use App\Exceptions\WrongCompanyReqsException;
use App\Services\CompanyReqsChecker\CompanyRepository;
use App\Services\CompanyReqsChecker\CompanyReqsCheckerInterface;
use App\User;
use App\ValueObjects\CompanyReqs;
use Src\Core\ValueObjects\Phone;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Hash;
use Src\Users\Management\Commands\CreateUser\CreateUserCommand;
use Src\Users\Management\Commands\UpdateUserAccess\UpdateUserAccessCommand;

class CreateCompanyHandler extends AbstractCreateElementHandler implements CreateElementHandlerInterface
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->companyRepository = new CompanyRepository();
        $this->dispatcher = app()->make(Dispatcher::class);

        parent::__construct('Company');
    }

    /**
     * @throws EntityAlreadyExistException
     * @throws Exception
     */
    public function handle($data)
    {
        $data = $this->validateData($data);

        $validator = function (int $hashId) {
            if (Company::withTrashed()->where('hash_id', $hashId)->first()) {
                return false;
            }

            if (User::withTrashed()->where('login', $this->getUserLogin($hashId))->first()) {
                return false;
            }

            return true;
        };

        $data['hash_id'] = $this->generateHashId(
            $validator,
            config('app.hash_generator.company.min'),
            config('app.hash_generator.company.max'),
            config('app.hash_generator.company.tries')
        );

        /** @var Company $created */
        $created = $this->createElement($data);

        $userId = $this->createUser($created);

        $created->related_user_id = $userId;
        $created->save();

        return $created;
    }

    /**
     * @throws Exception
     */
    protected function validateData($data): array
    {
        $existItem = Company::query()
            ->where('name', trim($data['name'] ?? ''))
            ->first();
        if ($existItem) {
            throw new EntityAlreadyExistException('Найден дубликат по названию компании');
        }

        $data = $this->validateReqs($data);

        return $this->validatePhoneNumber($data);
    }

    /**
     * @throws EntityAlreadyExistException
     * @throws Exception
     */
    protected function validateReqs($data): array
    {
        $companyReqs = new CompanyReqs($data['inn'] ?? '', $data['kpp'] ?? '', $data['ogrn'] ?? '');
        if ($companyReqs->isValidFormat()) {
            /** @var CompanyReqsCheckerInterface $companyReqsChecker */
            $companyReqsChecker = resolve(CompanyReqsCheckerInterface::class);
            if (!$companyReqsChecker->check($companyReqs)) {
                throw new WrongCompanyReqsException();
            }

            $data['reqs_validated'] = true;
        }

        $existItem = $this->companyRepository->findByReqs($companyReqs);
        if ($existItem) {
            throw new EntityAlreadyExistException('Найден дубликат компании по ИНН (+КПП) или ОГРН');
        }

        return $data;
    }

    /**
     * @throws Exception
     */
    protected function validatePhoneNumber(array $data): array
    {
        if (!array_key_exists('where_call', $data)) {
            return $data;
        }

        $phoneNumber = $data['where_call'];

        if (empty($phoneNumber)) {
            return $data;
        }

        $phone = new Phone($phoneNumber);

        if (!$phone->isValid()) {
            throw new Exception('Некорректный формат телефона, введите телефон в формате 7ХХХХХХХХХХ');
        }

        $data['where_call'] = $phone->getSanitized();

        return $data;
    }

    protected function getUserLogin(string $hashId): string
    {
        return '0' . $hashId;
    }

    /**
     * @throws Exception
     */
    protected function createUser(Company $created)
    {
        $companyHashId = $created->hash_id;
        $userLogin = $this->getUserLogin($companyHashId);

        $user = $this->dispatcher->dispatch(new CreateUserCommand(
            UserEntityType::company(),
            $userLogin,
            $companyHashId . '@ta-7.ru',
            $userLogin,
            Hash::make(date('H:i:s') . sha1($companyHashId)),
            12,
        ));

        $this->dispatcher->dispatch(new UpdateUserAccessCommand(
            $user,
            [UserRoleEnum::CLIENT],
            []
        ));

        return $user->id;
    }
}
