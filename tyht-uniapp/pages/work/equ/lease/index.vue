<template>
  <view class="container">
    <!-- 列表 -->
    <view v-for="(item, index) in leaseData" :key="index">
      <view v-if="item.length==0">
        <fq-empty empty-text="暂无数据"></fq-empty>
      </view>
      <!-- 列表样式 -->
      <view v-else>
        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">工程名称</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.epcName}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">设备名称</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.equName}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">设备型号</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.equType}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">是否有保险</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{isInsuranceStatus[item.isInsurance]}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">数量</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.total}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">单价</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{numConverter(item.price)}}元</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">租用天数</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.numberDays}}天</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">总价</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{numConverter(item.totalPrice)}}元</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">供货商</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.supplier}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">配件归属单位或个人</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.attribution}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">是否为公司操作员</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{isCompanyOperatorStatus[item.isCompanyOperator]}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">申报人</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.declarant}}</text>
            </view>
          </view>
        </view>

        <view class="order-item">
          <view class="order-content">
            <view class="">
              <text class="order-name">备注</text>
            </view>
            <view class="price">
              <text style="font-size: 14rpx;">{{item.remark}}</text>
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
        leaseData: [],
        // 字符转化
        isCompanyOperatorStatus: {
          "0": "否",
          "1": "是"
        },
        // 字符转化
        isInsuranceStatus: {
          "0": "无保险",
          "1": "有保险"
        }
      }
    },
    methods: {
      // 单位转化
      numConverter(val) {
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
      this.leaseData = [JSON.parse(options.item2)]
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
