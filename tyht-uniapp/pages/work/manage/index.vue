<template>
  <view>
    <view class="grid-body">
      <uni-grid :column="4" :showBorder="false">
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToManageSubcontract">
            <uni-icons type="gear" size="30"></uni-icons>
            <text class="text">分包项目</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToManageBidding">
            <uni-icons customPrefix="iconfont" type="icon-gongchengguanli" size="30"></uni-icons>
            <text class="text">投标项目</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToManageLease">
            <uni-icons customPrefix="iconfont" type="icon-gongchengguanli" size="30"></uni-icons>
            <text class="text">租赁项目</text>
          </view>
        </uni-grid-item>
      </uni-grid>
    </view>
  </view>
</template>
<script>
  export default {
    methods: {
      handleToManageSubcontract() {
        uni.navigateTo({
          url: '/pages/work/manage/manageSubcontract/index'
        })
      },

      handleToManageBidding() {
        uni.navigateTo({
          url: '/pages/work/manage/manageBidding/index'
        })
      },

      handleToManageLease() {
        uni.navigateTo({
          url: '/pages/work/manage/manageLease/index'
        })
      },
    }
  }
</script>
<style>
  /* #ifndef APP-NVUE */
  page {
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    background-color: #fff;
    min-height: 100%;
    height: auto;
  }

  view {
    font-size: 14px;
    line-height: inherit;
  }

  /* #endif */

  .text {
    text-align: center;
    font-size: 26rpx;
    margin-top: 10rpx;
  }

  .grid-item-box {
    flex: 1;
    /* #ifndef APP-NVUE */
    display: flex;
    /* #endif */
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 15px 0;
  }

  .uni-margin-wrap {
    width: 690rpx;
    width: 100%;
    ;
  }

  .swiper {
    height: 300rpx;
  }

  .swiper-box {
    height: 150px;
  }

  .swiper-item {
    /* #ifndef APP-NVUE */
    display: flex;
    /* #endif */
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: #fff;
    height: 300rpx;
    line-height: 300rpx;
  }

  @media screen and (min-width: 500px) {
    .uni-swiper-dot-box {
      width: 400px;
      /* #ifndef APP-NVUE */
      margin: 0 auto;
      /* #endif */
      margin-top: 8px;
    }
  }
</style>
