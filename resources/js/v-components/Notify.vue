<template>
    <div class="Notify">
        <a v-if="messages.length" @click.prevent="clearMsgs" href="#">Очистить всё</a>

        <div v-for="msg in messages" class="Notify__message">
            <i class="fa fa-info-circle"></i> {{ msg.message }}
        </div>
    </div>
</template>

<script>

    export default {
        data () {
            return {
                messages: [
                    {
                        id: 0,
                        user_id: 0,
                        message: 'Появился новый осмотр в очереди СДПО'
                    },
                    {
                        id: 0,
                        user_id: 0,
                        message: 'Появился новый осмотр в очереди СДПО'
                    },
                    {
                        id: 0,
                        user_id: 0,
                        message: 'Появился новый осмотр в очереди СДПО'
                    },
                    {
                        id: 0,
                        user_id: 0,
                        message: 'Появился новый осмотр в очереди СДПО'
                    },{
                        id: 0,
                        user_id: 0,
                        message: 'Появился новый осмотр в очереди СДПО'
                    },{
                        id: 0,
                        user_id: 0,
                        message: 'Появился новый осмотр в очереди СДПО'
                    },{
                        id: 0,
                        user_id: 0,
                        message: 'Появился новый осмотр в очереди СДПО'
                    },{
                        id: 0,
                        user_id: 0,
                        message: 'Появился новый осмотр в очереди СДПО'
                    },


                ]
            }
        },

        methods: {
            clearMsgs () {
                this.messages = []
            },

            playAudio () {
                let audio = new Audio('/notify.mp3')
                audio.play()
            },

            getNotifications () {

            }
        },

        mounted () {
            this.getNotifications()

            setInterval(() => {
                console.log(Math.random())
                //this.playAudio()
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
