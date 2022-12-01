<template>
  <view class="container">
    <!-- 列表 -->
    <view v-for="(item, index) in chData" :key="index">
      <view v-if="item.length==0">
        <fq-empty empty-text="暂无数据"></fq-empty>
      </view>
      <!-- 列表样式 -->
      <view v-else>
        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">案件编号</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.number}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">案件名称</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.name}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">案件简介</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.introduction}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">原告/被告</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{plaintiffOrDefendant[item.plaintiffOrDefendant]}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">对方单位</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.oppositeUnit}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">涉案金额</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{numberConverter(item.amount)}}元</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">受理法院</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.court}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">委托代理人</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.entrustedAgent}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">处理状态</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{status[item.status]}}</text>
            </view>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
  export default {
    data() {
      return {
        scrollH: 660,
        // 字符转化
        status: {
          '0': '处理中',
          '1': '胜诉',
          '2': '败诉'
        },
        plaintiffOrDefendant: {
          '0': '原告',
          '1': '被告'
        },
        chData: []
      }
    },
    methods: {
      // 单位转化
      numberConverter(val) {
        return val / 100
      },
      // 格式化时间
      dateFormat(time) {
        let date = new Date(time);
        let year = date.getFullYear();
        // 在日期格式中，月份是从0开始的，因此要加0，使用三元表达式在小于10的前面加0，以达到格式统一  如 09:11:05
        let month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
        let day = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        let hours = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
        let minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        let seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
        // 拼接
        // return year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
        return year + "-" + month + "-" + day;
      }
    },

    onLoad(options) {
      uni.getSystemInfo({
        success: res => {
          this.scrollH = res.windowHeight;
          // #ifdef MP
          this.scrollH -= 44;
          // #endif
        }
      });
      this.chData = [JSON.parse(options.item3)]
    }
  };
</script>

<style>
  .order-item {
    background-color: #fff;
    padding: 20rpx;
    border-radius: 15rpx;
    margin: 15rpx;
  }

  .order-top-view {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10rpx;
  }

  .order-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
</style>
