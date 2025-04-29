<?php

declare(strict_types=1);

namespace Src\Verification\Verifiers;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Src\Notifications\Notifier;
use Src\Notifications\SMSNotifier\SMSNotifier;
use Src\Verification\CodeGenerators\CodeGenerator;
use Src\Verification\CodeGenerators\NumberCodeGenerator;
use Src\Verification\Entities\Verification;
use Src\Verification\Exceptions\VerificationSendFailed;
use Src\Verification\Exceptions\VerificationsNotFound;
use Src\Verification\Repositories\VerificationRepository;
use Src\Verification\Verifier;

final class DefaultVerifier implements Verifier
{
    /** @var Notifier */
    private $notifier;

    /** @var string */
    private $message;

    /** @var CodeGenerator */
    private $codeGenerator;

    /** @var VerificationRepository */
    private $repository;

    public function __construct(
        NumberCodeGenerator $codeGenerator,
        SMSNotifier $notifier,
        VerificationRepository $repository
    ) {
        $this->codeGenerator = $codeGenerator;
        $this->notifier = $notifier;
        $this->repository = $repository;
        $this->message = 'Код для подтверждения действия на ta-7.ru: {code}';
    }

    public function setNotifier(Notifier $notifier): void
    {
        $this->notifier = $notifier;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setCodeGenerator(CodeGenerator $codeGenerator): void
    {
        $this->codeGenerator = $codeGenerator;
    }

    /**
     * @throws \Exception
     */
    public function create($subject): Verification
    {
        $id = Uuid::uuid4();
        $expiredAt = new DateTimeImmutable('+10 minutes');
        $nextAttemptAt = new DateTimeImmutable('+1 minute');
        $code = $this->codeGenerator->generate();

        $verification = new Verification($id, $subject, $code, 0, $expiredAt, $nextAttemptAt);
        $this->repository->add($verification);

        return $verification;
    }

    /**
     * @throws VerificationSendFailed
     */
    public function send(Verification $verification): void
    {
        if (! $this->notifier->notify($verification->getSubject(), str_replace('{code}', $verification->getCode(), $this->message))) {
            throw new VerificationSendFailed('Notification failed!');
        }
    }

    /**
     * @throws VerificationsNotFound
     */
    public function verify(string $code, $subject): bool
    {
        $verifications = $this->repository->getAvailableVerificationsBySubject($subject);

        if (count($verifications) === 0) {
            throw new VerificationsNotFound('Verifications not found!');
        }

        $usedVerification = array_filter($verifications, function (Verification $verification) use ($code) {
            return $verification->getCode() === $code;
        });

        if (count($usedVerification) > 0) {
            $this->repository->useVerificationsById(array_map(function (Verification $verification) {
                return $verification->getId()->toString();
            }, $usedVerification));

            return true;
        }

        return false;
    }

    /**
     * @throws VerificationsNotFound
     * @throws VerificationSendFailed
     */
    public function retry(UuidInterface $verificationId): void
    {
        $verification = $this->repository->getVerificationById($verificationId);

        if ($verification === null) {
            throw new VerificationsNotFound('Verifications not found!');
        }

        $verification->addAttempt();
        $this->send($verification);

        $this->repository->save($verification);
    }
}
