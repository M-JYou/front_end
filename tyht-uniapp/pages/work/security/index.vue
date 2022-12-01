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

      <!-- 安全人员 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;" :style="'height:' + scrollH + 'px;'">
          <view v-for="(item1, index1) in personData" :key="index1">
            <!-- 无数据提示 -->
            <view v-if="item1.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </view>
            <!-- 列表样式 -->
            <view class="order-item" @click="handleToPerson(item1)" v-else>
              <view class="order-top-view">
                <view class="order-store-name">项目名称：{{item1.name}}</view>
                <view class="order-status">{{dateFormat(item1.createTime)}}</view>
              </view>
            </view>
          </view>
        </scroll-view>
      </swiper-item>

      <!-- 安全培训 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;" :style="'height:' + scrollH + 'px;'">
          <view v-for="(item2, index2) in trainData" :key="index2">
            <!-- 无数据提示 -->
            <view v-if="item2.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </view>
            <!-- 列表样式 -->
            <view class="order-item" @click="handleToTrain(item2)" v-else>
              <view class="order-top-view">
                <view class="order-store-name">地址：{{item2.address}}</view>
                <view class="order-status">{{dateFormat(item2.createTime)}}</view>
              </view>
            </view>
          </view>
        </scroll-view>
      </swiper-item>

      <!-- 安全巡检 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;" :style="'height:' + scrollH + 'px;'">
          <view v-for="(item3, index3) in patrolData" :key="index3">
            <!-- 无数据提示 -->
            <view v-if="item3.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </view>
            <!-- 列表样式 -->
            <view class="order-item" @click="handleToPatrol(item3)" v-else>
              <view class="order-top-view">
                <view class="order-store-name">地址：{{item3.address}}</view>
                <view class="order-status">{{dateFormat(item3.createTime)}}</view>
              </view>
            </view>
          </view>
        </scroll-view>
      </swiper-item>

      <!-- 工程安全 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;" :style="'height:' + scrollH + 'px;'">
          <view v-for="(item4, index4) in securityEpcData" :key="index4">
            <!-- 无数据提示 -->
            <view v-if="item4.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </view>
            <!-- 列表样式 -->
            <view class="order-item" @click="handleToSecurityEpc(item4)" v-else>
              <view class="order-top-view">
                <view class="order-store-name">项目名称：{{item4.epcName}}</view>
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
    getSecurityPerson,
    getSecurityTrain,
    getSecurityPatrol,
    getSecurityEpc
  } from '@/api/work/security/security.js'
  export default {
    data() {
      return {
        tabIndex: 0,
        scrollInto: '',
        scrollH: 660,
        // 选项卡名称
        itemsArr: ['安全人员', '安全培训', '安全巡检', '工程安全'],

        // 安全人员数据
        // 是否有下一页
        personHasNext: null,
        personFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        personData: [],
        personURL: '/pages/work/security/person/index',

        // 安全培训数据
        // 是否有下一页
        trainHasNext: null,
        trainFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        trainData: [],
        trainURL: '/pages/work/security/train/index',

        // 安全巡检数据
        // 是否有下一页
        patrolHasNext: null,
        patrolFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        patrolData: [],
        patrolURL: '/pages/work/security/patrol/index',

        // 工程安全数据
        // 是否有下一页
        securityEpcHasNext: null,
        securityEpcFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        securityEpcData: [],
        securityEpcURL: '/pages/work/security/securityEpc/index',
      };
    },
    methods: {
      // 安全人员后端数据
      getPersonData() {
        getSecurityPerson(this.personFormData).then(res => {
          this.personHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.personFormData.pageNum == 1) {
              this.personData = [];
            }
            this.personData = [...this.personData, ...newList]
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
      },

      // 安全培训后端数据
      getTrainData() {
        getSecurityTrain(this.trainFormData).then(res => {
          this.trainHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.trainFormData.pageNum == 1) {
              this.trainData = [];
            }
            this.trainData = [...this.trainData, ...newList]
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
      },

      // 安全巡检后端数据
      getPatrolData() {
        getSecurityPatrol(this.patrolFormData).then(res => {
          this.patrolHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.patrolFormData.pageNum == 1) {
              this.patrolData = [];
            }
            this.patrolData = [...this.patrolData, ...newList]
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
      },

      // 工程安全后端数据
      getSecurityEpcData() {
        getSecurityEpc(this.securityEpcFormData).then(res => {
          this.securityEpcHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.securityEpcFormData.pageNum == 1) {
              this.securityEpcData = [];
            }
            this.securityEpcData = [...this.securityEpcData, ...newList]
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
      handleToPerson(item1) {
        uni.navigateTo({
          url: this.personURL + "?item1=" + JSON.stringify(item1)
        })
      },

      handleToTrain(item2) {
        uni.navigateTo({
          url: this.trainURL + "?item2=" + JSON.stringify(item2)
        })
      },

      handleToPatrol(item3) {
        uni.navigateTo({
          url: this.patrolURL + "?item3=" + JSON.stringify(item3)
        })
      },

      handleToSecurityEpc(item4) {
        uni.navigateTo({
          url: this.securityEpcURL + "?item4=" + JSON.stringify(item4)
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
        // console.log(index);
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
      this.getPersonData();
      this.getTrainData();
      this.getPatrolData();
      this.getSecurityEpcData();
      setTimeout(() => {}, 1000);
      uni.startPullDownRefresh();
    },

    onReachBottom() {
      // 上拉加载数据
      if (this.personHasNext) {
        // 当前数据条数少于总条数 则增加请求页数
        this.personFormData.pageNum++;
        this.getPersonData(); //调用加载数据方法
        // 显示提示框
        uni.showLoading({
          title: '加载中'
        })
      } else {
        console.log('已加载全部数据');
      };

      if (this.trainHasNext) {
        // 当前数据条数少于总条数 则增加请求页数
        this.trainFormData.pageNum++;
        this.getTrainData(); //调用加载数据方法
        // 显示提示框
        uni.showLoading({
          title: '加载中'
        })
      } else {
        console.log('已加载全部数据');
      };

      if (this.patrolHasNext) {
        // 当前数据条数少于总条数 则增加请求页数
        this.patrolFormData.pageNum++;
        this.getPatrolData(); //调用加载数据方法
        // 显示提示框
        uni.showLoading({
          title: '加载中'
        })
      } else {
        console.log('已加载全部数据');
      };

      if (this.securityEpcHasNext) {
        // 当前数据条数少于总条数 则增加请求页数
        this.securityEpcFormData.pageNum++;
        this.getSecurityEpcData(); //调用加载数据方法
        // 显示提示框
        uni.showLoading({
          title: '加载中'
        })
      } else {
        console.log('已加载全部数据');
      };
    },

    onPullDownRefresh() {
      this.personFormData.pageNum = 1;
      this.trainFormData.pageNum = 1;
      this.patrolFormData.pageNum = 1;
      this.securityEpcFormData.pageNum = 1;
      this.getPersonData();
      this.getTrainData();
      this.getPatrolData();
      this.getSecurityEpcData();
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
