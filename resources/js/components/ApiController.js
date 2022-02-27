import axios from 'axios'

export class ApiController {
    constructor () {
        this.client = axios.create({
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

    clearNotifies () {
        return this.client.post(`/api/notify/clear`);
    }

    getFieldHTML ({ field, model, default_value }) {
        return this.client.get(`/api/getField/${model}/${field}/${default_value}`);
    }
}
