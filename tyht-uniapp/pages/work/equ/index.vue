<template>
  <view class="container">
    <!-- 顶部选项卡 -->
    <scroll-view class="pagecontrol-top-scroll" scroll-x="true" scroll-with-animation :scroll-into-view="scrollInto">
      <view class="pagecontrol-top-title">
        <view v-for="(item, index) in itemsArr" :key="index" class="pagecontrol-top-text" @click="changeTab(index)"
          :id="'tab' + index">
          <view :class="tabIndex === index ? 'pagecontrol-top-selected' : 'pagecontrol-top-normal'">{{ item }}</view>
          <view class="pagecontrol-bottom-line"
            :class="tabIndex === index ? 'pagecontrol-bottom-line-show' : 'pagecontrol-bottom-line-visibility'"></view>
        </view>
      </view>
    </scroll-view>

    <!-- 内容 -->
    <swiper :duration="150" :current="tabIndex" @change="onChangeTab" :style="'height:' + scrollH + 'px;'">

      <!-- 自有设备 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;" :style="'height:' + scrollH + 'px;'">
          <view v-for="(item1, index1) in inventoryData" :key="index1">
            <!-- 无数据提示 -->
            <view v-if="item1.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </view>
            <!-- 列表样式 -->
            <view class="order-item" @click="handleToInventory(item1)" v-else>
              <view class="order-top-view">
                <view class="order-store-name">设备名称：{{item1.equName}}</view>
                <view class="order-status">{{dateFormat(item1.createTime)}}</view>
              </view>
            </view>
          </view>
        </scroll-view>
      </swiper-item>

      <!-- 设备租赁 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;" :style="'height:' + scrollH + 'px;'">
          <view v-for="(item2, index2) in leaseData" :key="index2">
            <!-- 无数据提示 -->
            <view v-if="item2.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </view>
            <!-- 列表样式 -->
            <view class="order-item" @click="handleToLease(item2)" v-else>
              <view class="order-top-view">
                <view class="order-store-name">设备名称：{{item2.equName}}</view>
                <view class="order-status">{{dateFormat(item2.createTime)}}</view>
              </view>
            </view>
          </view>
        </scroll-view>
      </swiper-item>
    </swiper>
  </view>
</template>

<script>
  import {
    getInventoryList,
    getLeaseList
  } from '@/api/work/equ/equ.js'
  export default {
    data() {
      return {
        tabIndex: 0,
        scrollInto: '',
        scrollH: 660,
        // 选项卡名称
        itemsArr: ['自有设备', '设备租赁'],

        // 自有设备数据
        // 是否有下一页
        inventoryHasNext: null,
        inventoryFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        // status: {
        //   "0": "停用",
        //   "1": "正常"
        // },
        inventoryData: [],
        inventoryURL: '/pages/work/equ/inventory/index',

        // 设备租赁数据
        // 是否有下一页
        leaseHasNext: null,
        leaseFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        // status: {
        //   "0": "停用",
        //   "1": "正常"
        // },
        leaseData: [],
        leaseURL: '/pages/work/equ/lease/index',
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
      },

      // 监听滑动
      onChangeTab(e) {
        this.changeTab(e.detail.current);
      },
      // 切换选项
      changeTab(index) {
        // console.log(index);
        if (this.tabIndex === index) {
          return;
        }
        this.tabIndex = index;
        // 滚动到指定元素
        this.scrollInto = 'tab' + index;
      },

      // 自有设备后端数据
      getInventoryData() {
        getInventoryList(this.inventoryFormData).then(res => {
          this.inventoryHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.inventoryFormData.pageNum == 1) {
              this.inventoryData = [];
            }
            this.inventoryData = [...this.inventoryData, ...newList]
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
      },

      // 设备租赁后端数据
      getLeaseData() {
        getLeaseList(this.leaseFormData).then(res => {
          this.leaseHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.leaseFormData.pageNum == 1) {
              this.leaseData = [];
            }
            this.leaseData = [...this.leaseData, ...newList]
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
      },

      // 点击跳转事件
      handleToInventory(item1) {
        uni.navigateTo({
          url: this.inventoryURL + "?item1=" + JSON.stringify(item1)
        })
      },

      handleToLease(item2) {
        uni.navigateTo({
          url: this.leaseURL + "?item2=" + JSON.stringify(item2)
        })
      }
    },

    onLoad() {
      uni.getSystemInfo({
        success: res => {
          // console.log('wuwuFQ:', res);
          this.scrollH = res.windowHeight - 40;
          // #ifdef MP
          this.scrollH -= 44;
          // #endif
          // console.log('wuwuFQ:', this.scrollH);
        }
      });
      this.getInventoryData();
      this.getLeaseData();
      setTimeout(() => {}, 1000);
      uni.startPullDownRefresh();
    },

    onReachBottom() {
      // 上拉加载数据
      if (this.inventoryHasNext) {
        // 当前数据条数少于总条数 则增加请求页数
        this.inventoryFormData.pageNum++;
        this.getInventoryData(); //调用加载数据方法
        // 显示提示框
        uni.showLoading({
          title: '加载中'
        })
      } else {
        console.log('已加载全部数据');
      };

      if (this.leaseHasNext) {
        // 当前数据条数少于总条数 则增加请求页数
        this.leaseFormData.pageNum++;
        this.getLeaseData(); //调用加载数据方法
        // 显示提示框
        uni.showLoading({
          title: '加载中'
        })
      } else {
        console.log('已加载全部数据');
      }
    },

    onPullDownRefresh() {
      this.inventoryFormData.pageNum = 1;
      this.leaseFormData.pageNum = 1;
      this.getInventoryData();
      this.getLeaseData();
      setTimeout(() => {
        uni.stopPullDownRefresh();
      }, 1000);
    }
  }
</script>

<style>
  .pagecontrol-top-scroll {
    height: 40px;
    width: 100%;
    white-space: nowrap;
    box-sizing: border-box;
    border-bottom-width: 1rpx;
    border-bottom-style: solid;
    border-bottom-color: #ededed;
  }

  .pagecontrol-top-title {
    height: 100%;
    width: 100%;
    display: flex;
    justify-content: space-around;
  }

  .pagecontrol-top-text {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
  }

  .pagecontrol-top-normal {
    color: black;
    font-size: 16px;
  }

  .pagecontrol-top-selected {
    color: #0abafa;
    font-size: 17px;
  }

  .pagecontrol-bottom-line {
    width: 100%;
    height: 1px;
    margin-top: 2px;
    background-color: #0abafa;
  }

  .pagecontrol-bottom-line-show {
    visibility: visible;
  }

  .pagecontrol-bottom-line-visibility {
    visibility: hidden;
  }

  .order-item {
    background-color: #ffffff;
    padding: 20rpx;
    border-radius: 15rpx;
    margin: 15rpx;
  }

  .order-top-view {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10rpx;
  }

  .order-store-name {
    font-size: 14rpx;
    font-weight: 500;
  }

  .order-status {
    font-size: 12rpx;
    color: #a5a5a5;
  }
</style>
