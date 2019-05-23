<template>
<div>
    <select @change="onChange($event)">
        <option selected disabled>Filter by</option>
        <option :value="channel" v-html="channelname"></option>
        <option v-for="(item, index) in data" :key="'option-' + index" :value="item.url" v-html="item.title"></option>
    </select>
</div>
</template>
<script>
export default {
    props: {
        data: {
            type: Array,
            required: true,
        },
        channel: {
            type: String,
            required: true,
        },
        channelname: {
            type: String,
            required: true,
        },        
    },
    methods: {
        onChange(event){
            const newLocation = event.target.value
            window.location =  newLocation;
        }
    },
}
</script>
