<template>
  <view class="personListItem">
    <view class="personList" v-for="(item,i) in biddingData" :key="item.id">
      <view class="wrap">
        <text class="wrapLeft">项目名称</text>
        <text class="wrapRight">{{item.name}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">招标单位</text>
        <text class="wrapRight">{{item.bidInvitationUnit}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">项目状态</text>
        <text class="wrapRight">{{status[item.status] }}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">开标时间</text>
        <text class="wrapRight">{{dateFormat(item.bidOpeningTime)}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">投标截止时间</text>
        <text class="wrapRight">{{dateFormat(item.endTime)}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">业主单位</text>
        <text class="wrapRight">{{item.ownerUnit}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">项目负责人</text>
        <text class="wrapRight">{{item.responsiblePerson}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">监理单位</text>
        <text class="wrapRight">{{item.supervisionUnit}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">金额</text>
        <text class="wrapRight">{{numConverter(item.totalPrice)}}元</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">招标方式</text>
        <text class="wrapRight">{{item.type}}</text>
      </view>
      <view class="line"></view>
    </view>
  </view>
</template>
<script>
  export default {
    data() {
      return {
        // 字符转化
        status: {
          '0': '近期招标',
          '1': '准备投标',
          '2': '已中标',
          '3': '未中标'
        },
        biddingData: [],
        query: {
          pageNum: 1,
          pageSize: 5
        },
      }
    },

    onLoad(options) {
      this.biddingData = [JSON.parse(options.item)]
      console.log(this.biddingData);
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
