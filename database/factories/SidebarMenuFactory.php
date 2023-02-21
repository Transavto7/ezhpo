<?php

/** @var Factory $factory */

use App\Model;
use App\SideBarMenuItem;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(SideBarMenuItem::class, function (Faker $faker) {
    return [
        [
            'id' => 1001,
            'title' => 'Провести мед. осмотр',
            'css_class' => 'bg-red text-white',
            'icon_class' => 'icon-padnote',
            'route_name' => '/profile/anketa?type=medic',
            'access_permissions' => 'medic_create'
        ],

        [
            'id' => 1002,
            'title' => 'Провести тех. осмотр',
            'css_class' => 'bg-blue text-white',
            'icon_class' => 'icon-padnote',
            'route_name' => '/profile/anketa?type=tech',
            'access_permissions' => 'tech_create'
        ],

        [
            'id' => 1003,
            'title' => 'Внести Отчёт с карты',
            'css_class' => 'bg-gray',
            'icon_class' => 'icon-padnote',
            'route_name' => '/profile/anketa?type=report_cart',
            'access_permissions' => 'map_report_create'
        ],

        [
            'id' => 1004,
            'title' => 'Внести запись в Реестр печати ПЛ',
            'css_class' => 'bg-gray',
            'icon_class' => 'icon-padnote',
            'route_name' => '/profile/anketa?type=report_cart',
            'access_permissions' => 'print_register_pl_create'
        ],

        [
            'id' => 1005,
            'title' => 'Внести Инструктаж БДД',
            'css_class' => 'bg-gray',
            'icon_class' => 'icon-padnote',
            'route_name' => '/profile/anketa?type=report_cart',
            'access_permissions' => 'journal_briefing_bdd_create'
        ],

        [
            'id' => 1006,
            'title' => 'Очередь утверждения',
            'css_class' => 'bg-gray',
            'icon_class' => 'fa fa-users',
            'route_name' => '/home/pak_queue',
            'access_permissions' => 'approval_queue_view, approval_queue_clear'
        ],

        [
            'id' => 1007,
            'title' => 'Добавить клиента',
            'css_class' => 'bg-info text-white',
            'icon_class' => 'icon-user',
            'route_name' => '/pages/add_client',
            'access_permissions' => 'client_create'
        ],
    ];
});
