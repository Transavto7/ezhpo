<template>
    <div v-if="messages.length" class="Notify">
        <div class="text-center">
            <h2 class="text-white">У вас новые уведомления!</h2>
            <br>
            <a @click.prevent="clearMsgs" class="btn btn-success" href="#">Очистить всё ({{ messages.length }})</a>
            <br><br>
        </div>

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
        max-width: 350px;
        margin: 0 auto;
    }

    .Notify {
        width: 100%;
        height: 100%;
        overflow-y: auto;
        background: rgba(0,0,0,.7);
        position: fixed;
        color: black;
        top: 0;
        left: 0;
        padding: 25px;
        z-index: 111;
        text-align: left;
        font-size: 14px;
    }
</style>
