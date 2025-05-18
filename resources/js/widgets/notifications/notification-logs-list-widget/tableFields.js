export default [
    {
        key: 'title',
        sortable: true,
        label: 'Уведомление',
        thClass: 'text-center',
        thStyle: { width: "60%" },
    },
    {
        key: 'action',
        sortable: true,
        label: 'Действие',
        thClass: 'text-center',
        tdClass: 'text-center',
    },
    {
        key: 'user',
        sortable: false,
        label: 'Пользователь',
        thClass: 'text-center',
        tdClass: 'text-center',
    },
    {
        key: 'created_at',
        label: 'Дата и время',
        sortable: true,
        thClass: 'text-center',
        tdClass: 'text-center',
        thStyle: { width: "150px" },
    },
]
