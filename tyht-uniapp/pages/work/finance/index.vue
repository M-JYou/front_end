<template>
  <view>
    <!-- 顶部选项卡 -->
    <scroll-view class="pagecontrol-top-scroll" scroll-x="true" scroll-with-animation :scroll-into-view="scrollInto">
      <view class="pagecontrol-top-title">
        <view v-for="(item, index) in tabBars" :key="index" class="pagecontrol-top-text" @click="changeTab(index)"
          :id="'tab' + index">
          <view :class="tabIndex === index ? 'pagecontrol-top-selected' : 'pagecontrol-top-normal'">{{ item}}
          </view>
          <view class="pagecontrol-bottom-line"
            :class="tabIndex === index ? 'pagecontrol-bottom-line-show' : 'pagecontrol-bottom-line-visibility'"></view>
        </view>
      </view>
    </scroll-view>

    <!-- 内容 -->
    <swiper :duration="150" :current="tabIndex" @change="onChangeTab" v-for="(item, index) in dataFiveRecords"
      class="swiper-box" :key="index">
      <swiper-item>
        <scroll-view scroll-y="true" show :style="'height:' + scrollH + 'px;'" style="background-color: #F5F5F5;">
          <!-- 无数据提示 -->
          <template v-if="item.today==null">
            <fq-empty empty-text="暂无数据"></fq-empty>
          </template>
          <template v-else>
            <!-- 列表 -->
            <view>
              <uni-list>
                <uni-list-item title="已收账款" :rightText="`${item.today.received/100}元`" />
                <uni-list-item title="已付账款" :rightText="`${item.today.paid/100}元`" />
                <uni-list-item title="余额" :rightText="`${item.today.remainingBalance/100}元`" />
                <uni-list-item title="应收账款" :rightText="`${item.today.receivable/100}元`" />
                <uni-list-item title="应付账款" :rightText="`${item.today.payable/100}元`" />
              </uni-list>
            </view>
          </template>
        </scroll-view>
      </swiper-item>

      <swiper-item>
        <scroll-view scroll-y="true" show :style="'height:' + scrollH + 'px;'" style="background-color: #F5F5F5;">
          <!-- 无数据提示 -->
          <template v-if="item.lastWeek==null">
            <fq-empty empty-text="暂无数据"></fq-empty>
          </template>
          <template v-else>
            <!-- 列表 -->
            <view>
              <uni-list>
                <uni-list-item title="已收账款" :rightText="`${item.lastWeek.received/100}元`" />
                <uni-list-item title="已付账款" :rightText="`${item.lastWeek.paid/100}元`" />
                <uni-list-item title="余额" :rightText="`${item.lastWeek.remainingBalance/100}元`" />
                <uni-list-item title="应收账款" :rightText="`${item.lastWeek.receivable/100}元`" />
                <uni-list-item title="应付账款" :rightText="`${item.lastWeek.payable/100}元`" />
              </uni-list>
            </view>
          </template>
        </scroll-view>
      </swiper-item>

      <swiper-item>
        <scroll-view scroll-y="true" show :style="'height:' + scrollH + 'px;'" style="background-color: #F5F5F5;">
          <!-- 无数据提示 -->
          <template v-if="item.lastMonth==null">
            <fq-empty empty-text="暂无数据"></fq-empty>
          </template>
          <template v-else>
            <!-- 列表 -->
            <view>
              <uni-list>
                <uni-list-item title="已收账款" :rightText="`${item.lastMonth.received/100}元`" />
                <uni-list-item title="已付账款" :rightText="`${item.lastMonth.paid/100}元`" />
                <uni-list-item title="余额" :rightText="`${item.lastMonth.remainingBalance/100}元`" />
                <uni-list-item title="应收账款" :rightText="`${item.lastMonth.receivable/100}元`" />
                <uni-list-item title="应付账款" :rightText="`${item.lastMonth.payable/100}元`" />
              </uni-list>
            </view>
          </template>
        </scroll-view>
      </swiper-item>

      <swiper-item>
        <scroll-view scroll-y="true" show :style="'height:' + scrollH + 'px;'" style="background-color: #F5F5F5;">
          <!-- 无数据提示 -->
          <template v-if="item.lastQuarter==null">
            <fq-empty empty-text="暂无数据"></fq-empty>
          </template>
          <template v-else>
            <!-- 列表 -->
            <view>
              <uni-list>
                <uni-list-item title="已收账款" :rightText="`${item.lastQuarter.received/100}元`" />
                <uni-list-item title="已付账款" :rightText="`${item.lastQuarter.paid/100}元`" />
                <uni-list-item title="余额" :rightText="`${item.lastQuarter.remainingBalance/100}元`" />
                <uni-list-item title="应收账款" :rightText="`${item.lastQuarter.receivable/100}元`" />
                <uni-list-item title="应付账款" :rightText="`${item.lastQuarter.payable/100}元`" />
              </uni-list>
            </view>
          </template>
        </scroll-view>
      </swiper-item>

      <swiper-item>
        <scroll-view scroll-y="true" show :style="'height:' + scrollH + 'px;'" style="background-color: #F5F5F5;">
          <!-- 无数据提示 -->
          <template v-if="item.lastYear==null">
            <fq-empty empty-text="暂无数据"></fq-empty>
          </template>
          <template v-else>
            <!-- 列表 -->
            <view>
              <uni-list>
                <uni-list-item title="已收账款" :rightText="`${item.lastYear.received/100}元`" />
                <uni-list-item title="已付账款" :rightText="`${item.lastYear.paid/100}元`" />
                <uni-list-item title="余额" :rightText="`${item.lastYear.remainingBalance/100}元`" />
                <uni-list-item title="应收账款" :rightText="`${item.lastYear.receivable/100}元`" />
                <uni-list-item title="应付账款" :rightText="`${item.lastYear.payable/100}元`" />
              </uni-list>
            </view>
          </template>
        </scroll-view>
      </swiper-item>
    </swiper>
    <!-- <fin-item :totalitems="totalitems"></fin-item> -->
    <view class="note" v-if="remark==null "></view>
    <view class="note" v-else>备注：{{remark}}</view>
    <fin-record :resSeven="resSeven" :opts="opts"></fin-record>
    <fin-redline :resRedline="resRedline" :opts="opts"></fin-redline>
  </view>
</template>
<script>
  // 导入外部api
  import {
    getSevenRecord,
    getFiveRecord,
    getRedLine
  } from '@/api/work/finance/finance.js'
  // 导入外部封装组件
  // import finItem from '@/components/finItem/fin-item.vue'
  // import fqEmpty from '@/components/fq-empty/fq-empty.vue'
  import finRecord from '@/components/finItem/fin-record.vue'
  import finRedline from '@/components/finItem/fin-redline.vue'

  export default {
    // 注册组件
    components: {
      finRecord,
      finRedline
    },
    data() {
      return {
        // 备注信息
        remark: '',
        // 七天收付记录数据
        dataFiveRecords: [],
        tabBars: ['今日', '上周', '上月', '上季', '去年'],
        tabIndex: 0,
        scrollInto: '',
        scrollH: 660,
        resSeven: {
          categories: [],
          series: [{
            show: false,
            name: "已收账款",
            data: []
          }, {
            show: false,
            name: "已付账款",
            data: []
          }, {
            show: true,
            name: "余额",
            data: []
          }, {
            show: false,
            name: "应收账款",
            data: []
          }, {
            show: false,
            name: "应付账款",
            data: []
          }],
          chartDataRecord: {}
        },
        // 盈亏红线数据
        resRedline: {
          categories: [],
          series: [{
            name: "盈亏红线",
            data: []
          }],
          // 盈亏记录折线图数据对象
          chartDataRedline: {}
        },

        // 折线图设置数据
        opts: {
          fontsize: 15,
          padding: [20, 10, 0, 15],
          // 图例相关的配置
          legend: {
            show: true,
            lineheight: 50,
          },

          xAxis: {

            disableGrid: true,
          },
          yAxis: {
            gridType: "dash",
            dashLength: 4,
          },
          extra: {
            line: {
              type: "straight",
              width: 2
            }
          }
        },
      }
    },

    //通过onLoad生命周期函数监听后端发送过来的api接口数据。
    onLoad(options) {
      // 获取系统信息
      uni.getSystemInfo({
        success: res => {
          // console.log('wuwuFQ:', res);
          this.scrollH = res.windowHeight - 40;
          // #ifdef MP
          this.scrollH -= 44;
          // #endif
          // console.log('wuwuFQ:', this.scrollH);
        }
      });
      // 获取x天内的盈亏记录
      getRedLine().then(res => {
        // 获取时间
        let count = res.data.length;
        for (let i = 0; i < count; i++) {
          let date = res.data[i].date;
          this.resRedline.categories.push(date)
        };
        // 获取盈亏红线数值
        for (let i = 0; i < count; i++) {
          let value = res.data[i].value;
          this.resRedline.series[0].data.push(value)
        }
      });

      // 获取七天数据
      getSevenRecord().then(res => {
        // 获取时间
        let count = res.data.paidList.length;
        for (let i = 0; i < count; i++) {
          let date = res.data.paidList[i].date;
          this.resSeven.categories.push(date);
        };
        // 获取已付账款数据
        let countPaidList = res.data.paidList.length;
        for (let i = 0; i < countPaidList; i++) {
          let value = res.data.paidList[i].value / 1000000;
          this.resSeven.series[1].data.push(value)
        };
        // 获取应付账款数据
        let countPayableList = res.data.payableList.length;
        for (let i = 0; i < countPayableList; i++) {
          let value = res.data.payableList[i].value / 1000000;
          this.resSeven.series[4].data.push(value);
        };
        // 获取应收账款数据
        let countReceivableList = res.data.receivableList.length;
        for (let i = 0; i < countReceivableList; i++) {
          let value = res.data.receivableList[i].value / 1000000;
          this.resSeven.series[3].data.push(value)
        };
        // 获取已收账款数据
        let countReceivedList = res.data.receivedList.length;
        for (let i = 0; i < countReceivedList; i++) {
          let value = res.data.receivedList[i].value / 1000000;
          this.resSeven.series[0].data.push(value)
        };
        // 获取余额数据
        let countRemainingBalanceList = res.data.remainingBalanceList.length;
        for (let i = 0; i < countRemainingBalanceList; i++) {
          let value = res.data.remainingBalanceList[i].value / 1000000;
          this.resSeven.series[2].data.push(value)
        }
      });

      // 获取 今日、上周、上月、上季和上年的5条收付记录
      getFiveRecord().then(res => {
        this.dataFiveRecords = [res.data]
        console.log(this.dataFiveRecords);
        if (this.dataFiveRecords.today) {
          this.remark = this.dataFiveRecords.today.remark
        } else {
          this.remark = this.dataFiveRecords.today
        }
      });

      setTimeout(function() {}, 1000);
      uni.startPullDownRefresh();
    },

    // 下拉刷新
    onPullDownRefresh() {
      // 获取盈亏红线
      getRedLine().then(res => {})
      // 获取七天数据
      getSevenRecord().then(res => {});
      // 获取 今日、上周、上月、上季和上年的5条收付记录
      getFiveRecord().then(res => {});
      setTimeout(function() {
        uni.stopPullDownRefresh();
      }, 1000)
    },

    onReady() {
      this.getServerDataRecord();
      this.getServerDataRedline()
    },
    methods: {
      // 监听滑动
      onChangeTab(e) {
        this.changeTab(e.detail.current);
      },
      // 切换选项
      changeTab(index) {
        // console.log(index);
        if (this.tabIndex === index) {
          return;
        }
        this.tabIndex = index;
        // 滚动到指定元素
        this.scrollInto = 'tab' + index;
      },
      // 折线图
      // 七天收付记录后台数据
      getServerDataRecord() {
        setTimeout(() => {
          //模拟服务器返回数据，如果数据格式和标准格式不同，需自行按下面的格式拼接
          this.resSeven.chartDataRecord = JSON.parse(JSON.stringify(this.resSeven));
        }, 500);
      },
      // x天内的盈亏红线后台数据
      getServerDataRedline() {
        setTimeout(() => {
          //模拟服务器返回数据，如果数据格式和标准格式不同，需自行按下面的格式拼接
          this.resRedline.chartDataRedline = JSON.parse(JSON.stringify(this.resRedline));
        }, 500)
      }
    },
  }
</script>
<style scoped lang="scss">
  .swiper-box {
    height: 431rpx;
  }

  .uni-list-item {
    background-color: #eee;
  }

  .pagecontrol-top-scroll {
    height: 40px;
    width: 100%;
    white-space: nowrap;
    box-sizing: border-box;
    border-bottom-width: 1rpx;
    border-bottom-style: solid;
    border-bottom-color: #ededed;
  }

  .pagecontrol-top-title {
    height: 100%;
    width: 100%;
    display: flex;
    justify-content: space-around;
  }

  .pagecontrol-top-text {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
  }

  .pagecontrol-top-normal {
    color: black;
    font-size: 16px;
  }

  .pagecontrol-top-selected {
    color: #0abafa;
    font-size: 17px;
  }

  .pagecontrol-bottom-line {
    width: 100%;
    height: 1px;
    margin-top: 2px;
    background-color: #0abafa;
  }

  .pagecontrol-bottom-line-show {
    visibility: visible;
  }

  .pagecontrol-bottom-line-visibility {
    visibility: hidden;
  }

  /* .order-item {
    background-color: #ffffff;
    padding: 20rpx;
    border-radius: 15rpx;
    margin: 15rpx;
  } */

  /* .order-top-view {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10rpx;
  } */

  /* .order-store-name { */
  /* font-size: 20rpx; */
  /* font-weight: 500; */
  /* } */

  /* .order-status { */
  /* font-size: 20rpx; */
  /* color: #a5a5a5; */
  /* } */

  .order-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .order-date {
    margin: 6rpx 0;
  }

  .note {
    display: flex;
    width: 80%;
    margin: 15rpx auto 15rpx;
    flex-wrap: wrap;
    border: 1rpx soild balck
  }
</style>
