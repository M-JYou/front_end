<template>
  <view class="hrListItem">
    <view class="hrList" v-for="(item,i) in hrData " :key="item.id">
      <view class="wrap">
        <text class="wrapLeft">工程名称</text>
        <text class="wrapRight">{{item.epcName}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">协作单位名称</text>
        <text class="wrapRight">{{item.name}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">协作单位联系人</text>
        <text class="wrapRight">{{item.contact}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">协作单位联系人电话</text>
        <text class="wrapRight" @click="()=>telFun(item.contactPhone)">{{item.contactPhone}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">队伍人数</text>
        <text class="wrapRight">{{item.headcount}}</text>
      </view>
      <view class="line"></view>
      <view class="wrap">
        <text class="wrapLeft">施工内容</text>
        <text class="wrapRight">{{item.constructionContent}}</text>
      </view>
      <view class="line"></view>
    </view>
  </view>
</template>
<script>
  // import {
  //   getCooperative
  // } from '@/api/work/hr/hr.js'
  export default {
    data() {
      return {
        hrData: [],
        query: {
          pageNum: 1,
          pageSize: 5
        },
      }
    },

    onLoad(options) {
      this.hrData = [JSON.parse(options.item)]
    },

    methods: {
      telFun(e) {
        const res = uni.getSystemInfoSync();
        // 模态框
        if (res.platform == 'ios') {
          uni.makePhoneCall({
            phoneNumber: e,
            success() {
              console.log('拨打成功');
            },
            fail() {
              console.log('拨打失败');
            }
          })
        } else {
          uni.showActionSheet({
            itemList: [e, '呼叫'],
            success: function(res) {
              console.log(res, 3333);
              if (res.tapIndex == 1) {
                uni.makePhoneCall({
                  phoneNumber: e,
                })
              }
            }
          })
        }
      }
    }
  }
</script>

<style scoped>
  .hrList {
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
