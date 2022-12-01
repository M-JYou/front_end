<template>
  <view class="personListItem">
    <view class="personList" v-for="(item,i) in leaseData" :key="item.id">
      <view class="wrap">
        <text class="wrapLeft">项目名称</text>
        <text class="wrapRight">{{item.name}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">租赁内容</text>
        <text class="wrapRight">{{item.leaseContent}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">租赁单位</text>
        <text class="wrapRight">{{item.leaseUnit}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">项目编号</text>
        <text class="wrapRight">{{item.number}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">预计金额</text>
        <text class="wrapRight">{{numConverter(item.price)}}元</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">业主单位</text>
        <text class="wrapRight">{{item.proprietorUnit}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">总包单位</text>
        <text class="wrapRight">{{item.totalPackageUnit}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">备注</text>
        <text class="wrapRight">{{item.remark}}</text>
      </view>
      <view class="line"></view>
    </view>
  </view>
</template>
<script>
  export default {
    data() {
      return {
        leaseData: [],
        query: {
          pageNum: 1,
          pageSize: 5
        },
      }
    },

    onLoad(options) {
      this.leaseData = [JSON.parse(options.item)]
    },

    methods: {
      // 单位换算
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
    }
  }
</script>

<style scoped>
  .personList {
    display: flex;

    flex-direction: column;
    width: 100%;
  }

  .hrName {
    font-size: 30rpx;
    font-weight: 500;
    line-height: 30rpx;
    margin: 30rpx 0 10rpx 30rpx;
    flex-wrap: wrap;
  }

  .wrapbox {
    width: 100%;
  }

  .wrap {
    display: flex;
    color: gray;
    font-size: 26rpx;
    margin: 16rpx 30rpx 16rpx;
  }

  .wrapLeft {
    flex-grow: 1;

  }

  .wrapRight {
    flex-grow: 1;
    text-align: right;
  }

  .line {
    display: flex;
    width: 100%;
    margin: 0 auto;
    height: 0.1rpx;
    background-color: lightgray;
  }
</style>
