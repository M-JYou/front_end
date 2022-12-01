<template>
  <view class="work-container">
    <!-- 宫格组件 -->
    <!-- <uni-section title="系统管理" type="line"></uni-section> -->
    <view class="grid-body">
      <uni-grid :column="4" :showBorder="false">
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToFinance">
            <uni-icons type="wallet" size="30"></uni-icons>
            <text class="text">财务管理</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToEpc">
            <uni-icons customPrefix="iconfont" type="icon-gongchengguanli" size="30"></uni-icons>
            <text class="text">工程管理</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToHr">
            <uni-icons type="person" size="30"></uni-icons>
            <text class="text">人资管理</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToEqu">
            <uni-icons type="gear" size="30"></uni-icons>
            <text class="text">设备管理</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToMaterial">
            <uni-icons customPrefix="iconfont" type="icon-cailiaoguanli" size="30"></uni-icons>
            <text class="text">材料管理</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToSecurity">
            <uni-icons customPrefix="iconfont" type="icon-anquan" size="30"></uni-icons>
            <text class="text">安全管理</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToLaw">
            <uni-icons customPrefix="iconfont" type="icon-fawu" size="30"></uni-icons>
            <text class="text">法务管理</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToManage">
            <uni-icons type="shop" size="30"></uni-icons>
            <text class="text">经营管理</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToTest1">
            <uni-icons customPrefix="iconfont" type="icon-gongchengguanli" size="30"></uni-icons>
            <text class="text">测试页面1</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToTest2">
            <uni-icons customPrefix="iconfont" type="icon-gongchengguanli" size="30"></uni-icons>
            <text class="text">测试页面2</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToTest3">
            <uni-icons customPrefix="iconfont" type="icon-gongchengguanli" size="30"></uni-icons>
            <text class="text">测试页面3</text>
          </view>
        </uni-grid-item>
        <uni-grid-item>
          <view class="grid-item-box" @click="handleToTest4">
            <uni-icons customPrefix="iconfont" type="icon-gongchengguanli" size="30"></uni-icons>
            <text class="text">测试页面4</text>
          </view>
        </uni-grid-item>
      </uni-grid>
    </view>
  </view>
</template>

<script>
  export default {
    data() {
      return {

      }
    },
    methods: {
      handleToFinance(e) {
        this.$tab.navigateTo("/pages/work/finance/index")
      },
      handleToEpc(e) {
        this.$tab.navigateTo("/pages/work/epc/index")
      },
      handleToHr(e) {
        this.$tab.navigateTo("/pages/work/hr/index")
      },
      handleToEqu(e) {
        this.$tab.navigateTo("/pages/work/equ/index")
      },
      handleToMaterial(e) {
        this.$tab.navigateTo("/pages/work/material/index")
      },
      handleToSecurity(e) {
        this.$tab.navigateTo("/pages/work/security/index")
      },
      handleToLaw(e) {
        this.$tab.navigateTo("/pages/work/law/index")
      },
      handleToManage(e) {
        this.$tab.navigateTo("/pages/work/manage/index")
      },
      handleToTest1(e) {
        this.$tab.navigateTo("/pages/work/test/index1")
      },
      handleToTest2(e) {
        this.$tab.navigateTo("/pages/work/test/index2")
      },
      handleToTest3() {
        this.$tab.navigateTo("/pages/work/test/index3")
      },
      handleToTest4() {
        this.$tab.navigateTo("/pages/work/test/index4")
      }
    }
  }
</script>

<style lang="scss">
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
