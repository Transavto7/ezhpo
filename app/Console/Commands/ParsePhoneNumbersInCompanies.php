<?php

namespace App\Console\Commands;

use App\Company;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Illuminate\Console\Command;
use libphonenumber\PhoneNumberFormat;

class ParsePhoneNumbersInCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:phones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсит и фиксит формат номеров телефонов';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var Company $company */
        foreach (Company::cursor() as $company) {
            try {
                if (!is_null($company->where_call)) {
                    $phone = phone_sanitizing($company->where_call);

                    if (str_starts_with($phone, '+9')) {
                        $phone = '+7' . ltrim($phone, '+');
                    }

                    if (!strlen(phone_sanitizing($phone)) or $phone === '+7') {
                        $phone = phone_sanitizing($company->where_call_name);
                    }

                    if (!mb_strlen(phone_sanitizing($phone))) {
                        continue;
                    }

                    $phoneNumber = PhoneNumber::parse($phone, 'RU');
                    $company->where_call = $phoneNumber->format(PhoneNumberFormat::E164);
                    $company->save();
                }
            } catch (PhoneNumberParseException $exception) {
                dd($company->id, $company->where_call, phone_sanitizing($phone), $exception->getMessage());
            }
        }
    }
}
