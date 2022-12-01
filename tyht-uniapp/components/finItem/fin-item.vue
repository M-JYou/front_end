<template>
  <view class="fin-item">
    <!-- 顶部导航栏 -->
    <view class="horizonal-tab">
      <!-- scroll-view:可滚动视图区域 -->
      <scroll-view scroll-x="true" scroll-with-animation class="scroll-tab">
        <block v-for="(item,index) in totalitems.tabBars" :key="item.id">
          <view class="scroll-tab-item" :class="{'active': totalitems.tabIndex==index}" @tap="toggleTab(index)">
            {{item.name}}
            <view class="scroll-tab-line"></view>
          </view>
        </block>
      </scroll-view>
    </view>
    <!-- 内容区 -->
    <view>
      <!-- 滑块视图 -->
      <swiper :current="totalitems.tabIndex" @change="tabChange" class="tabindex"
        v-for="item in totalitems.itemsFiveRecord" :key="item.id">
        <!-- current:当前所在滑块的index -->
        <swiper-item v-if="item.today == null">
          <view class="promptText">
            <text>暂时无数据</text><text class="refresh" @click="refresh()">重试</text>
          </view>
        </swiper-item>
        <swiper-item v-else>
          <uni-list class="uni-list-item" :key="item.id">
            <uni-list-item title="已收账款" :rightText="`${item.today.received/100}元`" />
            <uni-list-item title="已付账款" :rightText="`${item.today.paid/100}元`" />
            <uni-list-item title="余额" :rightText="`${item.today.remainingBalance/100}元`" />
            <uni-list-item title="应收账款" :rightText="`${item.today.receivable/100}元`" />
            <uni-list-item title="应付账款" :rightText="`${item.today.payable/100}元`" />
          </uni-list>
        </swiper-item>

        <swiper-item v-if="item.lastWeek== null ">
          <view class="promptText">
            <text>暂时无数据</text><text class="refresh" @click="refresh()">重试</text>
          </view>
        </swiper-item>
        <swiper-item v-else>
          <uni-list class="uni-list-item" :key="item.id">
            <uni-list-item title="已收账款" :rightText="`${item.lastWeek.received/100}元`" />
            <uni-list-item title="已付账款" :rightText="`${item.lastWeek.paid/100}元`" />
            <uni-list-item title="余额" :rightText="`${item.lastWeek.remainingBalance/100}元`" />
            <uni-list-item title="应收账款" :rightText="`${item.lastWeek.receivable/100}元`" />
            <uni-list-item title="应付账款" :rightText="`${item.lastWeek.payable/100}元`" />
          </uni-list>
        </swiper-item>

        <swiper-item v-if="item.lastMonth== null ">
          <view class="promptText">
            <text>暂时无数据</text><text class="refresh" @click="refresh()">重试</text>
          </view>
        </swiper-item>
        <swiper-item v-else>
          <uni-list class="uni-list-item" :key="item.id">
            <uni-list-item title="已收账款" :rightText="`${item.lastMonth.received/100}元`" />
            <uni-list-item title="已付账款" :rightText="`${item.lastMonth.paid/100}元`" />
            <uni-list-item title="余额" :rightText="`${item.lastMonth.remainingBalance/100}元`" />
            <uni-list-item title="应收账款" :rightText="`${item.lastMonth.receivable/100}元`" />
            <uni-list-item title="应付账款" :rightText="`${item.lastMonth.payable/100}元`" />
          </uni-list>
        </swiper-item>

        <swiper-item v-if="item.lastQuarter==null">
          <view class="promptText">
            <text>暂时无数据</text><text class="refresh" @click="refresh()">重试</text>
          </view>
        </swiper-item>
        <swiper-item v-else>
          <uni-list class="uni-list-item" :key="item.id">
            <uni-list-item title="已收账款" :rightText="`${item.lastQuarter.received/100}元`" />
            <uni-list-item title="已付账款" :rightText="`${item.lastQuarter.paid/100}元`" />
            <uni-list-item title="余额" :rightText="`${item.lastQuarter.remainingBalance/100}元`" />
            <uni-list-item title="应收账款" :rightText="`${item.lastQuarter.receivable/100}元`" />
            <uni-list-item title="应付账款" :rightText="`${item.lastQuarter.payable/100}元`" />
          </uni-list>
        </swiper-item>

        <swiper-item v-if="item.lastYear== null">
          <view class="promptText">
            <text>暂时无数据</text><text class="refresh" @click="refresh()">重试</text>
          </view>
        </swiper-item>
        <swiper-item v-else>
          <uni-list class="uni-list-item" :key="item.id">
            <uni-list-item title="已收账款" :rightText="`${item.lastYear.received/100}元`" />
            <uni-list-item title="已付账款" :rightText="`${item.lastYear.paid/100}元`" />
            <uni-list-item title="余额" :rightText="`${item.lastYear.remainingBalance/100}元`" />
            <uni-list-item title="应收账款" :rightText="`${item.lastYear.receivable/100}元`" />
            <uni-list-item title="应付账款" :rightText="`${item.lastYear.payable/100}元`" />
          </uni-list>
        </swiper-item>
      </swiper>
    </view>
  </view>
</template>

<script>
  export default {
    props: {
      totalitems: Object
    },
    methods: {
      // 点击刷新方法
      refresh() {
        uni.redirectTo({
          url: '/pages/work/finance/index'
        })
      },
      //切换选项卡
      toggleTab(index) {
        this.totalitems.tabIndex = index;
      },

      //滑动切换swiper
      tabChange(e) {
        // console.log(e);
        this.totalitems.tabIndex = e.detail.current;
      }
    }
  }
</script>

<style>
  .promptText {
    width: 100%;
    display: block;
    background-color: #eee;
    text-align: center;
    line-height: 430rpx;
  }

  .refresh {
    padding-left: 10rpx;
    color: gray;
  }

  .horizonal-tab .active {
    color: red;
  }

  .scroll-tab {
    white-space: nowrap;
    /* 必要，导航栏才能横向*/
    border-bottom: 1rpx solid #eee;
    text-align: center;
  }

  .tabindex {
    width: 100%;
    height: 430rpx;
  }

  .uni-list-item {
    background-color: #eee;
  }

  .scroll-tab-item {
    display: inline-block;
    /* 必要，导航栏才能横向*/
    margin: 20rpx 30rpx 0 30rpx;
  }

  .active .scroll-tab-line {
    border-bottom: 5rpx solid red;
    border-top: 5rpx solid red;
    border-radius: 20rpx;
    width: 70rpx;
  }
</style>
