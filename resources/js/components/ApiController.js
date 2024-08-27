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


    getGraphReport ({ pv_id, date_from, date_to, type_anketa }) {
        const params = {
            pv_id,
            date_to,
            date_from,
            type_anketa,
        }

        return this.client
            .get(`/api/reports/graph_pv`, { params })
            .then(response => {
                return response.data
            })
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
