<template>
  <view class="fin-redline">
    <view class="redLine">
      <text>获取七天内的盈亏红线(单位：万元)</text>
    </view>
    <view class="charts-box">
      <qiun-data-charts type="line" :loadingType=0 :tapLegend="false" :opts="opts"
        :chartData="resRedline.chartDataRedline" :canvas2d=true canvasId="EOAYdFmRTydSamAJZMWIPZnhxKLcVMSi" />
    </view>
  </view>
</template>

<script>
  export default {
    props: {
      resRedline: Object,
      opts: Object
    }
  }
</script>

<style>
  .redLine {
    text-align: center;
    margin: 10rpx auto 10rpx;
  }
</style>
