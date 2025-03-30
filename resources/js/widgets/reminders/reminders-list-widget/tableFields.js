export default [
    {
        key: 'title',
        label: 'Наименование',
        thClass: 'text-center',
        thStyle: { width: "150px" },
    },
    {
        key: 'action',
        sortable: true,
        label: 'Действие(триггер)',
        thClass: 'text-center',
        tdClass: 'text-center',
        thStyle: { width: "150px" },
    },
    {
        key: 'context',
        label: 'Контекст',
        sortable: false,
        thClass: 'text-center',
        tdClass: 'text-center',
    },
    {
        key: 'updated_at',
        label: 'Дата обновления',
        sortable: true,
        thClass: 'text-center',
        tdClass: 'text-center',
        thStyle: { width: "150px" },
    },
    {
        key: 'actions',
        label: 'Действия',
        thClass: 'text-center',
        thStyle: { width: "200px" },
    },
]
