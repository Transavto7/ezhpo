import axios from 'axios'

export class ApiController {
    constructor () {
        this.client = axios.create({
            baseURL: location.origin,
            headers: {
                Authorization: 'Bearer ' + API_TOKEN
            }
        })
    }

    updateModelProperty ({ item_model, item_id, item_field, new_value }) {
        return this.client.put(`/api/update-ddate/${item_model}/${item_id}/${item_field}`, { new_value }).then(response => {
            const data = response.data

            return data
        })
    }

    saveDoc(docType, data) {
        return this.client.put(`/api/update-doc/${docType}`, data).then(response => {
            return response.data
        })
    }

    getNotify () {
        return this.client.get(`/api/notify`).then(response => {
            const data = response.data

            return data
        })
    }

    getGraphReport ({ pv_id, date_from, date_to, date_from_time, date_to_time, type_anketa }) {
        let pv_id_str = ''

        pv_id.forEach((item, i) => {
            pv_id_str += `pv_id[${i}]=${item}&`;
        })

        let dopParams = (date_to_time && date_from_time) ? `&date_from_time=${date_from_time}&date_to_time=${date_to_time}` : '';

        // Ты чё еблан нахуй?
        return this.client.get(`/api/report/graph_pv?filter=1&${pv_id_str}date_from=${date_from}&date_to=${date_to}&api=1${dopParams}&type_anketa=${type_anketa}`).then(response => {
            const data = response.data

            return data
        })
    }

    clearNotifies () {
        return this.client.post(`/api/notify/clear`);
    }

    getFieldHTML ({ field, model, default_value }) {
        return this.client.get(`/api/getField/${model}/${field}/${default_value}`);
    }

    getFindModel ({ model, params }) {
        return this.client.get(`/api/find/${model}`, {
            params: {
                ...params
            }
        });
    }

    saveFieldsVisible(params) {
        return this.client.post('/api/fields/visible', {
            params,
        });
    }
}
