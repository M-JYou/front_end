<template>
  <lb-tabbar ref="tabbar"
    :value="active"
    :animate="animate">
    <lb-tabbar-item class="tabbarsed" v-for="item in tabbars"
      :key="item.name"
      :name="item.name"
      :icon="item.iconActive&&active==item.name?item.iconActive:item.icon"
      :dot="item.dot"
      :info="item.info"
      :raisede="item.raisede"
      icon-prefix="iconfont"
      @click="handleTabbarItemClick"
	  :class="{'raisedeaction':item.raisede}"
	  >
      {{ item.text }}
    </lb-tabbar-item>
  </lb-tabbar>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex'
export default {
  data () {
    return {

    }
  },
  computed: {
    ...mapGetters(['active', 'animate', 'tabbars']),
  },
  methods: {
    ...mapMutations(['SET_ACTIVE']),
    handleTabbarItemClick (e) {
      const name = e.name
      if (name === 'plus') {
        uni.showToast({
          title: '发布',
          icon: 'none'
        })
      } else if(name=="scan"){
		  
		  // #ifdef APP-PLUS
		  var token = uni.getStorageSync('token');
		  var points = uni.getStorageSync("userInfo")?uni.getStorageSync("userInfo").credit_number:'';
		  if(token){
			  this.module.gotoNativePage({
				'token':token,
				'points':points}
			  );
		  }else {
			uni.showToast({
			  title: '没有登录，无法使用扫码功能',
			  icon: 'none'
			})  
		  }
		  // #endif
		  
	  } else {
        const tabbar = this.tabbars.find(item => item.name === name)
        uni.switchTab({
          url: tabbar.path,
          success: () => {
            // 切换后重新设置，因为不在在触发页面的created生命周期
            this.SET_ACTIVE(name)
          }
        })
      }
    }
  }
}

</script>

<style lang="scss" scoped>
	.raisedeaction{
		/* #ifdef MP-WEIXIN */
		margin: 0 8%;
		/* #endif */
	}
	
	// 底部导航颜色
	.tabbarsed{
		background-color: #141F31;
	}
</style>
