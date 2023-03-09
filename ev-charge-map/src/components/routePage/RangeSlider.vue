<template>
  <div id="rangeSlider">
    <div class="range">
      <div class="row">
        <div class="text-body1">
          Search Range
        </div>
        <div class="text-body1">
          Miles: {{ range }}
        </div>
      </div>
      <input type="range" min="1" max="10" v-model="data.range" class="slider">
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue'

const props = defineProps({
  range: {
    type: Number,
    default: 1
  }
})

const data = reactive({
  range: props.range
})

const emit = defineEmits(['update:range'])

watch(() => data.range, () => emit('update:range', Number(data.range)))
</script>

<style lang="scss" scoped>
#rangeSlider {
  width: 100%;

  .slider {
    width: 100%;
    height: 7px;
    border-radius: 5px;
  }

  .range {
    margin-top: 10px;
    margin-bottom: 10px;
    text-align: center;
    font-weight: 500;
  }

  .row {
    font-weight: 500;
    padding: 10px;
    font-size: 15px;
    justify-content: space-between;
  }
}
</style>
