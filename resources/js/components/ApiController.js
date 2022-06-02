import axios from 'axios'

export class ApiController {
    constructor () {
        this.client = axios.create({
            baseURL: (location.hostname === 'localhost' ? 'http://' : 'https://') + location.host,
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

    getNotify () {
        return this.client.get(`/api/notify`).then(response => {
            const data = response.data

            return data
        })
    }

    getGraphReport ({ pv_id, date_from, date_to }) {
        let pv_id_str = ''

        pv_id.forEach((item, i) => {
            pv_id_str += `pv_id[${i}]=${item}&`;
        })

        return this.client.get(`/api/report/graph_pv?filter=1&${pv_id_str}date_from=${date_from}&date_to=${date_to}&api=1`).then(response => {
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
}
