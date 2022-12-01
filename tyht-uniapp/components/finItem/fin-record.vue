<template>
  <view class="fin-record">
    <!-- 折线图 -->
    <view class="records">
      <text>最近七天收付记录(单位：万元)</text>
    </view>
    <view class="charts-box">
      <qiun-data-charts type="line" :loadingType=0 :opts="opts" @getIndex="Switchinglines"
        :chartData="resSeven.chartDataRecord" :tapLegend="false" :canvas2d=true
        canvasId="vwQaQvNDSBUvAWYzdgjSzjnzQNQjTPwv" />
    </view>
  </view>
</template>

<script>
  export default {
    props: {
      resSeven: Object,
      opts: Object
    },
    methods: {
      Switchinglines(e) {
        for (let i = 0; i < this.resSeven.series.length; i++) {
          let obj = this.resSeven.series[i];
          if (i == e.legendIndex) {
            obj.show = true;
          } else {
            obj.show = false
          }
        };
        this.resSeven.chartDataRecord = JSON.parse(JSON.stringify(this.resSeven));
      }
    }
  }
</script>

<style>
  .records {
    text-align: center;
    margin-bottom: 10rpx;
  }
</style>
