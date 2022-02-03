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

    getFieldHTML ({ field, model, default_value }) {
        return this.client.get(`/api/getField/${model}/${field}/${default_value}`);
    }
}
