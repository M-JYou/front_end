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
      <!-- 分包项目 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;" :style="'height:' + scrollH + 'px;'">
          <view v-for="(item1, index1) in subcontractData" :key="index1">
            <!-- 无数据提示 -->
            <template v-if="item1.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </template>

            <template v-else>
              <!-- 列表 -->
              <view>
                <!-- 列表样式 -->
                <view class="order-item">
                  <view class="order-top-view">
                    <view class="order-store-name">项目名称：{{item1.name}}</view>
                    <view class="order-status">{{dateFormat(item1.createTime)}}</view>
                  </view>
                </view>
              </view>
            </template>
          </view>
        </scroll-view>
      </swiper-item>

      <!-- 投标项目 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;">
          <view v-for="(item2, index2) in biddingData" :key="index2">
            <!-- 无数据提示 -->
            <template v-if="item2.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </template>

            <template v-else>
              <!-- 列表 -->
              <view>
                <!-- 二级选项卡 -->
                <scroll-view class="pagecontrol-top-scroll" scroll-x="true" scroll-with-animation
                  :scroll-into-view="scrollInto">
                  <view class="pagecontrol-top-title">
                    <view v-for="(item, index) in secArr" :key="index" class="pagecontrol-top-text"
                      @click="changeTab(index)" :id="'tab' + index">
                      <view :class="tabIndex === index ? 'pagecontrol-top-selected' : 'pagecontrol-top-normal'">
                        {{ item }}
                      </view>
                      <view class="pagecontrol-bottom-line"
                        :class="tabIndex === index ? 'pagecontrol-bottom-line-show' : 'pagecontrol-bottom-line-visibility'">
                      </view>
                    </view>
                  </view>
                </scroll-view>

                <!-- 内容 -->
                <swiper :duration="150" :current="tabIndex" @change="onChangeTab">
                  <!-- 分包项目 -->
                  <swiper-item>
                    <scroll-view style="background-color: #F5F5F5;">
                      <view v-for="(item1, index1) in subcontractData" :key="index1">
                        <!-- 无数据提示 -->
                        <template v-if="item1.length==0">
                          <fq-empty empty-text="暂无数据"></fq-empty>
                        </template>

                        <template v-else>
                          <!-- 列表 -->
                          <view>
                            <!-- 列表样式 -->
                            <view class="order-item">
                              <view class="order-top-view">
                                <view class="order-store-name">项目名称：{{item1.name}}</view>
                                <view class="order-status">{{dateFormat(item1.createTime)}}</view>
                              </view>
                            </view>
                          </view>
                        </template>
                      </view>
                    </scroll-view>
                  </swiper-item>

                  <!-- 投标项目 -->
                  <swiper-item>
                    <scroll-view style="background-color: #F5F5F5;">
                      <view v-for="(item2, index2) in biddingData" :key="index2">
                        <!-- 无数据提示 -->
                        <template v-if="item2.length==0">
                          <fq-empty empty-text="暂无数据"></fq-empty>
                        </template>

                        <template v-else>
                          <!-- 列表 -->
                          <view>
                            <!-- 顶部选项卡 -->
                            <scroll-view class="pagecontrol-top-scroll" scroll-x="true" scroll-with-animation
                              :scroll-into-view="scrollInto">
                              <view class="pagecontrol-top-title">
                                <view v-for="(item, index) in itemsArr" :key="index" class="pagecontrol-top-text"
                                  @click="changeTab(index)" :id="'tab' + index">
                                  <view
                                    :class="tabIndex === index ? 'pagecontrol-top-selected' : 'pagecontrol-top-normal'">
                                    {{ item }}
                                  </view>
                                  <view class="pagecontrol-bottom-line"
                                    :class="tabIndex === index ? 'pagecontrol-bottom-line-show' : 'pagecontrol-bottom-line-visibility'">
                                  </view>
                                </view>
                              </view>
                            </scroll-view>



                            <!-- 列表样式 -->
                            <!-- <view class="order-item">
                              <view class="order-top-view">
                                <view class="order-store-name">项目名称：{{item2.name}}</view>
                                <view class="order-status">{{dateFormat(item2.createTime)}}</view>
                              </view>
                            </view> -->
                          </view>
                        </template>
                      </view>
                    </scroll-view>
                  </swiper-item>

                  <!-- 租赁项目 -->
                  <swiper-item>
                    <scroll-view style="background-color: #F5F5F5;">
                      <view v-for="(item3, index3) in leaseData" :key="index3">
                        <!-- 无数据提示 -->
                        <template v-if="item3.length==0">
                          <fq-empty empty-text="暂无数据"></fq-empty>
                        </template>

                        <template v-else>
                          <!-- 列表 -->
                          <view>
                            <!-- 列表样式 -->
                            <view class="order-item">
                              <view class="order-top-view">
                                <view class="order-store-name">项目名称：{{item3.name}}</view>
                                <view class="order-status">{{dateFormat(item3.createTime)}}</view>
                              </view>
                            </view>
                          </view>
                        </template>
                      </view>
                    </scroll-view>
                  </swiper-item>
                </swiper>
              </view>
            </template>
          </view>
        </scroll-view>
      </swiper-item>

      <!-- 租赁项目 -->
      <swiper-item>
        <scroll-view style="background-color: #F5F5F5;">
          <view v-for="(item3, index3) in leaseData" :key="index3">
            <!-- 无数据提示 -->
            <template v-if="item3.length==0">
              <fq-empty empty-text="暂无数据"></fq-empty>
            </template>

            <template v-else>
              <!-- 列表 -->
              <view>
                <!-- 列表样式 -->
                <view class="order-item">
                  <view class="order-top-view">
                    <view class="order-store-name">项目名称：{{item3.name}}</view>
                    <view class="order-status">{{dateFormat(item3.createTime)}}</view>
                  </view>
                </view>
              </view>
            </template>
          </view>
        </scroll-view>
      </swiper-item>
    </swiper>
  </view>
</template>

<script>
  import {
    getManageSubcontract,
    getManageBidding,
    getManageLease
  } from '@/api/work/manage/manage.js'
  export default {
    data() {
      return {
        tabIndex: 0,
        scrollInto: '',
        scrollH: 660,
        secArr: ['跟踪项目', '近期招标', '准备投标', '已中标', '未中标'],
        itemsArr: ['分包项目', '投标项目', '租赁项目'],
        // 分包项目
        // 是否有下一页
        subcontractHasNext: null,
        subcontractFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        subcontractData: [],

        // 投标项目
        // 是否有下一页
        biddinghHasNext: null,
        biddingFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        biddingData: [],

        // 租赁项目
        // 是否有下一页
        leaseHasNext: null,
        leaseFormData: {
          pageNum: 1, //第几页
          pageSize: 10 //每页10条数据
        },
        leaseData: [],
      };
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

      // 投标项目数据
      getBiddingData() {
        getManageBidding(this.biddingFormData).then(res => {
          this.biddinghHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.biddingFormData.pageNum == 1) {
              this.biddingData = [];
            }
            this.biddingData = [...this.biddingData, ...newList]
            // console.log(this.subcontractData.length);
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
      },

      // 分包项目数据
      getSubcontractData() {
        getManageSubcontract(this.subcontractFormData).then(res => {
          this.subcontractHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.subcontractFormData.pageNum == 1) {
              this.subcontractData = [];
            }
            this.subcontractData = [...this.subcontractData, ...newList]
            // console.log(this.subcontractData.length);
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
      },

      // 租赁项目数据
      getLeaseData() {
        getManageLease(this.leaseFormData).then(res => {
          this.leaseHasNext = res.data.hasNext
          // 请求数据成功
          if (res.code == 200) {
            const newList = res.data.list
            if (this.leaseFormData.pageNum == 1) {
              this.leaseData = [];
            }
            this.leaseData = [...this.leaseData, ...newList]
            // console.log(this.subcontractData.length);
            // 关闭提示框
            setTimeout(function() {
              uni.hideLoading();
            }, 1000);
          } else {
            console.log("数据请求失败")
          }
        })
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
      this.getLeaseData();
      this.getBiddingData();
      this.getSubcontractData();
      setTimeout(() => {}, 1000);
      uni.startPullDownRefresh();
    },

    onPullDownRefresh() {
      this.leaseFormData.pageNum = 1;
      this.biddingFormData.pageNum = 1;
      this.subcontractFormData.pageNum = 1;
      this.getLeaseData();
      this.getBiddingData();
      this.getSubcontractData();
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
    font-size: 20rpx;
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
    margin: 8rpx;
  }

  .order-top-view {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10rpx;
  }

  .order-store-name {
    font-size: 30rpx;
    font-weight: 500;
  }

  .order-status {
    font-size: 30rpx;
    line-height: 30rpx;
    color: #a5a5a5;
  }

  .order-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .order-date {
    margin: 6rpx 0;
  }

  .bottom-buttons {
    margin-top: 10rpx;
    display: flex;
    justify-content: flex-end;
    align-items: center;
  }
</style>
