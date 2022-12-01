<template>
	<view class="pages">
		<uni-nav-bar :fixed="true" background-color="#FFFFFF"
			:status-bar="true" :border="false" title="更改昵称" leftIcon="left" @clickLeft="back" color="#333333" size="34" rightText="保存" @clickRight="preserve">
		</uni-nav-bar>
		<view class="content">
			<view class="form">
				<uni-forms ref="baseForm" :modelValue="form">
					<uni-forms-item label="">
						<uni-easyinput v-model="form.name"/>
					</uni-forms-item>
					<view class="tips">好的名字可以让你的朋友更容易记住你</view>
				</uni-forms>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				form:{
					name:''
				},
				userInfo:{}
			};
		},
		onLoad() {
			this.userInfo = uni.getStorageSync("userInfo")
			this.form.name = this.userInfo.name
		},
		onShow() {
			
		},
		methods: {
			//更改昵称
			preserve(){
				if(this.form.name!=''){
					this.$commonApi.updateMemberName({
						name:this.form.name
					}).then(res => {
						if (res.code == 200) {
							this.$.toast(res.msg)
							this.userInfo.name = this.form.name
							uni.setStorage({
								key: "userInfo",
								data: this.userInfo
							})
							let pages = getCurrentPages(); //获取所有页面栈实例列表
							let nowPage = pages[pages.length - 1]; //当前页页面实例
							let prevPage = pages[pages.length - 2]; //上一页页面实例
							prevPage.$vm.userInfo.name = this.form.name
							setTimeout(()=>{
								uni.navigateBack({
									delta:1
								})
							},800)
						} else this.$.toast(res.msg)
					})
				}else this.$.toast('昵称不能为空')
			},
			back(){
				uni.navigateBack({
					delta:1
				})
			}
		}
	};
</script>

<style lang="scss" scoped>
	@import "index.scss";
</style>
