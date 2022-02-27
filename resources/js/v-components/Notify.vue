<template>
    <div class="Notify">
        <a v-if="messages.length" @click.prevent="clearMsgs" href="#">Очистить всё ({{ messages.length }})</a>

        <div v-for="msg in messages" class="Notify__message">
            <i class="fa fa-info-circle"></i> {{ msg.message }}
        </div>
    </div>
</template>

<script>
    import {ApiController} from "../components/ApiController";

    const API = new ApiController()

    export default {
        data () {
            return {
                messages: [],
                inited: 0
            }
        },

        methods: {
            clearMsgs () {
                API.clearNotifies().then(r => {
                    this.messages = []
                })
            },

            playAudio () {
                let audio = new Audio('/notify.mp3')
                audio.play()
            },

            async getNotifications () {
                let data = await API.getNotify()

                if(this.messages.length !== data.length && this.inited) {
                    this.playAudio()
                }

                this.messages = data
            }
        },

        async mounted () {
            await this.getNotifications()

            this.inited = 1

            setInterval(() => {
                this.getNotifications()
            }, 5000)
        }
    }
</script>

<style scoped type="text/css">
    .Notify__message {
        padding: 5px;
        box-shadow: 0px 5px 10px rgba(0,0,0,.2);
        background: white;
        border: 1px solid #e9e9e9;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .Notify {
        width: 300px;
        max-height: 300px;
        overflow-y: auto;
        position: fixed;
        color: black;
        top: 20px;
        right: 20px;
        z-index: 111;
        text-align: left;
        font-size: 14px;
    }
</style>
