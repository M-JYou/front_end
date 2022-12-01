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

      <!-- 债务清欠 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;" :style="'height:' + scrollH + 'px;'">
          <view v-for="(item1, index1) in debtData" :key="index1">
            <!-- 无数据提示 -->
            <view v-if="item1.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </view>
            <!-- 列表样式 -->
            <view class="order-item" @click="handleToDebt(item1)" v-else>
              <view class="order-top-view">
                <view class="order-store-name">欠款单位：{{item1.unit}}</view>
                <view class="order-status">{{dateFormat(item1.createTime)}}</view>
              </view>
            </view>
          </view>
        </scroll-view>
      </swiper-item>

      <!-- 合同评审 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;" :style="'height:' + scrollH + 'px;'">
          <view v-for="(item2, index2) in crData" :key="index2">
            <!-- 无数据提示 -->
            <view v-if="item2.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </view>
            <!-- 列表样式 -->
            <view class="order-item" @click="handleToCr(item2)" v-else>
              <view class="order-top-view">
                <view class="order-store-name">合同名称：{{item2.name}}</view>
                <view class="order-status">{{dateFormat(item2.createTime)}}</view>
              </view>
            </view>
          </view>
        </scroll-view>
      </swiper-item>

      <!-- 案件处理 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;" :style="'height:' + scrollH + 'px;'">
          <view v-for="(item3, index3) in chData" :key="index3">
            <!-- 无数据提示 -->
            <view v-if="item3.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </view>
            <!-- 列表样式 -->
            <view class="order-item" @click="handleToCh(item3)" v-else>
              <view class="order-top-view">
                <view class="order-store-name">案件名称：{{item3.name}}</view>
                <view class="order-status">{{dateFormat(item3.createTime)}}</view>
              </view>
            </view>
          </view>
        </scroll-view>
      </swiper-item>

      <!-- 紧急事件 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;" :style="'height:' + scrollH + 'px;'">
          <view v-for="(item4, index4) in exigencyData" :key="index4">
            <!-- 无数据提示 -->
            <view v-if="item4.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </view>
            <!-- 列表样式 -->
            <view class="order-item" @click="handleToExigency(item4)" v-else>
              <view class="order-top-view">
                <view class="order-store-name">事件名称：{{item4.name}}</view>
                <view class="order-status">{{dateFormat(item4.createTime)}}</view>
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
    getDebtDispose,
    getLawCr,
    getLawCh,
    getLawExigency
  } from '@/api/work/law/law.js'
  export default {
    data() {
      return {
        tabIndex: 0,
        scrollInto: '',
        scrollH: 660,
        // 选项卡名称
        itemsArr: ['债务清欠', '合同评审', '案件处理', '紧急事件'],

        // 债务清欠数据
        // 是否有下一页
        debtHasNext: null,
        debtFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        debtData: [],
        debtURL: '/pages/work/law/debt/index',

        // 合同评审数据
        // 是否有下一页
        crHasNext: null,
        crFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        crData: [],
        crURL: '/pages/work/law/cr/index',

        // 案件处理数据
        // 是否有下一页
        chHasNext: null,
        chFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        chData: [],
        chURL: '/pages/work/law/ch/index',

        // 紧急事件数据
        // 是否有下一页
        exigencyHasNext: null,
        exigencyFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        exigencyData: [],
        exigencyURL: '/pages/work/law/exigency/index',
      };
    },
    methods: {
      // 债务清欠后端数据
      getDebtData() {
        getDebtDispose(this.debtFormData).then(res => {
          this.debtHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.debtFormData.pageNum == 1) {
              this.debtData = [];
            }
            this.debtData = [...this.debtData, ...newList]
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
      },

      // 合同评审后端数据
      getLawCrData() {
        getLawCr(this.crFormData).then(res => {
          this.crHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.crFormData.pageNum == 1) {
              this.crData = [];
            }
            this.crData = [...this.crData, ...newList]
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
      },

      // 案件处理后端数据
      getLawChData() {
        getLawCh(this.chFormData).then(res => {
          this.chHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.chFormData.pageNum == 1) {
              this.chData = [];
            }
            this.chData = [...this.chData, ...newList]
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
      },

      // 紧急事件后端数据
      getLawExigencyData() {
        getLawExigency(this.exigencyFormData).then(res => {
          this.exigencyHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.exigencyFormData.pageNum == 1) {
              this.exigencyData = [];
            }
            this.exigencyData = [...this.exigencyData, ...newList]
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
      handleToDebt(item1) {
        uni.navigateTo({
          url: this.debtURL + "?item1=" + JSON.stringify(item1)
        })
      },

      handleToCr(item2) {
        uni.navigateTo({
          url: this.crURL + "?item2=" + JSON.stringify(item2)
        })
      },

      handleToCh(item3) {
        uni.navigateTo({
          url: this.chURL + "?item3=" + JSON.stringify(item3)
        })
      },

      handleToExigency(item4) {
        uni.navigateTo({
          url: this.exigencyURL + "?item4=" + JSON.stringify(item4)
        })
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
      },

      // 监听滑动
      onChangeTab(e) {
        this.changeTab(e.detail.current);
      },
      // 切换选项
      changeTab(index) {
        if (this.tabIndex === index) {
          return;
        }
        this.tabIndex = index;
        // 滚动到指定元素
        this.scrollInto = 'tab' + index;
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
      this.getDebtData();
      this.getLawCrData();
      this.getLawChData();
      this.getLawExigencyData();
      setTimeout(() => {}, 1000);
      uni.startPullDownRefresh();
    },

    onReachBottom() {
      // 上拉加载数据
      if (this.debtHasNext) {
        // 当前数据条数少于总条数 则增加请求页数
        this.debtFormData.pageNum++;
        this.getDebtData(); //调用加载数据方法
        // 显示提示框
        uni.showLoading({
          title: '加载中'
        })
      } else {
        console.log('已加载全部数据');
      };

      if (this.crHasNext) {
        // 当前数据条数少于总条数 则增加请求页数
        this.crFormData.pageNum++;
        this.getLawCrData(); //调用加载数据方法
        // 显示提示框
        uni.showLoading({
          title: '加载中'
        })
      } else {
        console.log('已加载全部数据');
      };

      if (this.chHasNext) {
        // 当前数据条数少于总条数 则增加请求页数
        this.chFormData.pageNum++;
        this.getLawChData(); //调用加载数据方法
        // 显示提示框
        uni.showLoading({
          title: '加载中'
        })
      } else {
        console.log('已加载全部数据');
      };

      if (this.exigencyHasNext) {
        // 当前数据条数少于总条数 则增加请求页数
        this.exigencyFormData.pageNum++;
        this.getLawExigencyData(); //调用加载数据方法
        // 显示提示框
        uni.showLoading({
          title: '加载中'
        })
      } else {
        console.log('已加载全部数据');
      };
    },

    onPullDownRefresh() {
      this.debtFormData.pageNum = 1;
      this.crFormData.pageNum = 1;
      this.chFormData.pageNum = 1;
      this.exigencyFormData.pageNum = 1;
      this.getDebtData();
      this.getLawCrData();
      this.getLawChData();
      this.getLawExigencyData();
      setTimeout(() => {
        uni.stopPullDownRefresh();
      }, 1000);
    }
  };
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
