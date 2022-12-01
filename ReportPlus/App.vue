<!-- 
	作者：黄思豪
	联系方式：1051495009@qq.com
 -->
<script>
	export default {
		mpType: 'app',
		  onLaunch() {
		    this.$Common.checkUpdateVersion();
		  },
		  onShow() {
		    console.log('App Show');
		  },
		  onHide() {
		    console.log('App Hide');
		  },
	}
</script>

<style>
	/*每个页面公共css */
	@import "./static/css/common.wxss";
	@import "./static/css/icon.wxss";
	@import "./static/css/icon_ui.wxss";
</style>
