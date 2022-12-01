<template>
  <view class="hrListItem">
    <view class="hrList" v-for="(item,i) in hrData " :key="item.id">
      <text class="hrName">{{item.deptName}}</text>
      <view class="line"></view>
      <view v-for="(itemList,j) in item.staffList" class="wrapbox" :key="itemList.id">
        <view class="wrap">
          <text class="wrapLeft">姓名：{{itemList.name}}</text>
          <text class="wrapRight" v-if="itemList.phoneNumber==''"></text>
          <text class="wrapRight" @click="()=>telFun(itemList.phoneNumber)" v-else>手机号码:{{itemList.phoneNumber}}</text>
          </text>
        </view>
        <view class="line"></view>
      </view>
    </view>
  </view>
</template>
<script>
  import {
    getStaff
  } from '@/api/work/hr/hr.js'
  export default {
    data() {
      return {
        total: null,
        hrData: [],
        formData: {
          pageNum: 1,
          pageSize: 5
        },
      }
    },

    methods: {
      getHrData() {
        getStaff().then(res => {
          this.hrData = res.data
          console.log(this.hrData);
        })
      },

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
    },

    onLoad() {
      this.getHrData();
      setTimeout(() => {}, 1000);
      uni.startPullDownRefresh();
    },

    onPullDownRefresh() {
      this.getHrData();
      setTimeout(() => {
        uni.stopPullDownRefresh();
      }, 1000);
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
    margin-right: 1%;
    align-items: center;
    justify-content: center;
    text-align: right;
    flex-wrap: nowrap;
  }

  .phoneNumber {
    align-items: center;
    justify-content: center;
  }

  .line {
    display: flex;
    width: 100%;
    margin: 0 auto;
    height: 0.1rpx;
    background-color: lightgray;
  }
</style>
