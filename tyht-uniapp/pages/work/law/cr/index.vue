<template>
  <view class="container">
    <!-- 列表 -->
    <view v-for="(item, index) in crData" :key="index">
      <view v-if="item.length==0">
        <fq-empty empty-text="暂无数据"></fq-empty>
      </view>
      <!-- 列表样式 -->
      <view v-else>
        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">合同编号</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.number }}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">合同名称</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.name}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">评审时间</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{dateFormat(item.reviewDate)}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">评审人</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.reviewPersonnel}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">评审状态</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{status[item.status]}}</text>
            </view>
          </view>
        </view>
        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">合同附件</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.contractAttachment}}</text>
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
          '0': '未评审',
          '1': '评审通过',
          '2': '评审未通过'
        },
        crData: []
      }
    },
    methods: {
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
      this.crData = [JSON.parse(options.item2)]
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
