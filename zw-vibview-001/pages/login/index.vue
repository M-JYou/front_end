<template>
	<view class="pages">
		<view class="content">
			<view class="labeltop">
				<image
					src="data:image/png;base64,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"
					class="logoimg"></image>
				<view class="title">欢迎登录卓微科技</view>
			</view>

			<view class="form">
				<view class="formboxsed">
					<view class="formboxs">
						<uni-forms ref="form">
							<view class="formitem">
								<view class="item divflex">
									<uni-easyinput type="" v-model="Account" placeholder="请输入您的账号" />
								</view>
							</view>
							<view class="formitem">
								<view class="item divflex">
									<uni-easyinput type="password" v-model="Password" placeholder="请输入密码" />
								</view>
							</view>
							<u-button @click="submit" class="btnli btn-login" :ripple="true" ripple-bg-color="#6c8aac">登
								录</u-button>
						</uni-forms>
					</view>
				</view>
				<view class="agreement">
					<text class="tet">注册即代表您同意</text>
					<text @click="openPage('/pages/content/index?code=MEMBER')" class="tet blue">《卓微科技用户协议》</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		getLogin
	} from '@/api/api.js'
	export default {
		data() {
			return {
				Account: '',
				Password: '',
				second: '60',
				is_second: false,
				timer: null
			};
		},
		onLoad() {

		},


		onload() {
			uni.setStorageSync('first_falg', true);
		},
		onShow() {

		},
		methods: {
			submit() {
				if (this.Account && this.Password) {
					const params = {
						Account: this.Account,
						Password: this.Password,
						Strategy: "app",
						IdentType: 1
					};
					// console.log(params.Password,111)
					getLogin(params).then(res => {
						if (res.statusCode === 200) {
							uni.setStorageSync("token", res.data.token);
							uni.setStorageSync("account", this.Account);
							uni.switchTab({
								url: '/pages/index/index'
							});

						}
						if (res.statusCode == 0) {
							uni.showModal({
								title: "密码或账号错误"
							})
						}
					});
				} else {
					if (this.Account === "" && this.Password === "") {
						uni.showModal({
							title: "账号或密码不能为空",
							textAlign: "center",
						})
					}

				}



			},


			openPage(url, type) {
				if (type == 'reLaunch') {
					uni.reLaunch({
						url: url
					})
				} else {
					uni.navigateTo({
						url: url
					})
				}
			}
		}
	};
</script>

<style lang="scss" scoped>
	@import "index.scss";
</style>
