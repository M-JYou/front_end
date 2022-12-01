<template>
	<view class="pages">
		<view class="hadersboxs">
			<view class="haderscontents">
				<view class="" @click="back">
					<image class="imgsss"
						src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgEAYAAAAj6qa3AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAAAZiS0dEAAAAAAAA+UO7fwAAAAlwSFlzAAAASAAAAEgARslrPgAAAhVJREFUaN7tmL1LQmEUxv0AkRCHGgSnFqFRAoMWlyAQwSknFRocnMSGxnR1zKWptSFaHV2cBCX6B4TEzZZIi/DrPA2nKxh6GnrfDklneRAul9/vue9974su1//8jQEAYHsbBAJls9o8vydOIFAwyAW021iaSkWbz544AGBri7PZhDilkjavOXECgXw+FqvXZfF+n3N3V5v75+IAAK+X8/ZWFh8MOPf2tLkNibvdnNfXsvjzM2c0qs1tWPzqShZ/eeFXIxbT5jZcQLUqi7+9sXg8rs1rWLxclsXHY85EQpvXnDiBQMWiLD6b8XXptDavOXEAwOkpJ9Fq8fmcM5PR5jUnTiDQycniya4cp5BCQZvXnDgAIJXinEzWrnYCgc7PtXkNix8dcb6/y+96uazNa1j88JBzNJLFLy+1eb+Ox8xt3O7P231zv8dHbWFrw084meScTtduegQC5XLavPaK+BSUP3uTCV93fKzNa68IAMDFhbwnDIec+/vavJaLqNXkIp6eeEVEItq8lgrweDjv7uRzQbfLGQppc5svwvmnh0CgRkMuotPhDAS0uS0VEQxyPjzIRTQaTnHa3OaLAACEw5y9nrxH3NxwOueNDRpn81tshuJUq9q8los4OOB8fZWLODvT5rVXBID/E6XLWRH5/EJ45dzfc3q92rz2igCwdKIkEKjV4h87O9p8v1fE0orw+7V5NmY+AHD+7Cto9IgxAAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDIyLTA3LTIxVDExOjM4OjE5KzA4OjAwRIxWywAAACV0RVh0ZGF0ZTptb2RpZnkAMjAyMi0wNy0yMVQxMTozODoxOSswODowMDXR7ncAAABNdEVYdHN2ZzpiYXNlLXVyaQBmaWxlOi8vL2hvbWUvYWRtaW4vaWNvbi1mb250L3RtcC9pY29uXzFhdW82eDllaGE3L3p1b2ppYW50b3Uuc3Zn6Oj+MwAAAABJRU5ErkJggg=="
						mode=""></image>
				</view>
				<view class="">{{data.pointname}} -趋势图</view>
				<view class=""> </view>
			</view>
		</view>


		<view class="leave_cont">
			<view class="ul">
				<view class="li" @click="openDatePicker('stime')">
					<text>开始时间:</text>
					<view class="flex1">

						<view class="date">{{stime}}</view>
					</view>
				</view>
				<view class="li" @click="openDatePicker('etime')">
					<text>结束时间:</text>
					<view class="flex1">
						<view class="date">{{etime}}</view>
					</view>
				</view>
			</view>

			<view class="headerrights">
				<view class="rights-lefts" @click="query">
					<image class="leftsimgs"
						src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgEAYAAAAj6qa3AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAAAZiS0dEAAAAAAAA+UO7fwAAAAlwSFlzAAAASAAAAEgARslrPgAAAwNJREFUaN7tWM8rdFEYPmfyW4yUMFYWUxPZ2PpVpCgRZWVDFA3xX1iQrb2NlQUmEqVYsEB+FCWl/JpsxEhkcJ5v8Tp4x9UM7p3zfX3zbp7O7c77Ps9zzpx73iNEIv7vkE4lhoKCSkqiUUEBoccjpJBC6ufBoICAQDAoXdIlXeGwaUN+Kbi+HgCAiQnCqytECwUFdXdHg6kpGre309jlMq3ra8EAgNJSwuVla3XPz4Q7O4Rzc4SBAAnd2KDxw4O1MdvbhFVVpvVGCG9uJry95ayPjwm7uwlzc2PLl5n5NvNaOIunJ0K/35xwBQXV1EREXl74TI2MEKak2GOwy0U4NMQN0PUGB+MnHADg8xGGQnxm+vriU7+hgTAc5obU1jpvgIKCmp/nMzA25nhhSyP8fj4BBwf8a2O78MpKXvD0lDA93YwBUhKv9XXOq6vLoYLj43zmBwbiLdyaV2Mj57W05FCh8/PXKoqwqMi4Aa+bLfG5uXnbGxQUlNttU4Hs7MjPm2nh1jwXFznP8vLv5vl80pJCCunx8IfBoGnB1jw/8IKAQCTvnxhA2WBaX4wuvPcyUkghv8/7swEQELi85OPCQtNSrXlG8vrA+9f5AQAXF3wT/P4Ss123goJKTrZrE4zSbQUChHqptbaaNoCWel0dDdxuWgmrq9ROh0K21SFHq6v5LntyQpiWZko/1V9b4+eAnh6HCy4s8IKjo3EXrqCgensjP892NWFRDCgpIfzQ/jrtPBOuL1oeHwl1N6r/CnEIKtjSwgkoRQSHh/XmZE8dKQn7+3kXqCMYpHr5+XEzgBNsa+NXWTqOjgg7OwlzcmLLl5HxlhcAsLXF82rDz874c90NmjBCQUGVlRGRlRVYhu7bNzcJZ2cJp6d5V3d/b/37w0PCmhp6Py+Pxnt7/L39fWNG8JnUXdrkJOH1NWIKbcDMDGFHx1d9/l9vBCeqDyzFxYQVFfqSk9DrJUxN/Vn+aEZkZZn2wfHgRuzuEuqTq89nml/8jADwfrvs9Zrmk4hEJOLfiD+Hp7UjbUsF/QAAACV0RVh0ZGF0ZTpjcmVhdGUAMjAyMi0wNy0yMVQxNDoyNzowMCswODowMDAUJNgAAAAldEVYdGRhdGU6bW9kaWZ5ADIwMjItMDctMjFUMTQ6Mjc6MDArMDg6MDBBSZxkAAAASXRFWHRzdmc6YmFzZS11cmkAZmlsZTovLy9ob21lL2FkbWluL2ljb24tZm9udC90bXAvaWNvbl9pam1leGI3YWh5ay9zb3VzdW8uc3ZnlOKnXAAAAABJRU5ErkJggg=="
						mode=""></image>
					<text>查询</text>
				</view>
				<view class="rights-rights" @click="goFrequency">
					<image class="rightsimgs"
						src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgEAYAAAAj6qa3AAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAAAZiS0dEAAAAAAAA+UO7fwAAAAlwSFlzAAAASAAAAEgARslrPgAAAxBJREFUaN7tWD1La0EQnU1eChFjVMRGEzAmIH4UtlomoJhG0F7yG9Kof0ALbW0MVhaCQkBiI6IWGisttDAGK8FENITYiXHnFScb2PeUfG2SJ+9Oc5jN7szO2Xs3cy6RZZZZZtl/bKJZiZiZmUdG4AUCxMTEHg8JEiT6+jCeywGfn/H75SX801NhEzZh+/hoNWEVFmq3AxcXgff3rFk2y5Ily0QCfiwGPDvD+MODPj+Xw/j6Ovzu7lbX+U3hXi82en0Nv1AAbm9jfGpKEVRZvMFB4NIS8PVVJ2R+vtV1Fzc6Oalv8OYGGxwbM5vH5ULcaBS+lMCVlRYV7vWWCpcsWR4dATs6Gp5fsmQZieivSjjcpMLtdv1RVydefeFYt7AA7O+vbf3GBvbx/q5ftg0jQF1uhUK9j3rp8IpE1LZeXbq3t8CDgwYTkEyqy81MvNoJKMWRLFmGQnq88gdjqy7B6Cg8vx+4tVU/pYZMkCBxeAgnnYY/N2eMAAQMBOBks0DVqLTehBBCCCnRQMXjGA0GzRHAxMRuNzCVQsLPz1YX/rUlk0C3u9zMXxXHFCRI9PbCeXkxvmdBgoTDYSZYJgNULfb3VvkTQEREb29Ap9Ns9YkEcG0Nd83QUM2hBAkSnZ1w8nlzBDAxcToNHBgwVjsTE09Pw3l8RAEnJ/URoV7Vpydz+5QsWQaD+t+Mz2csPjMzt7eXRBEzM6fTwOHh6vZ5dYV1m5uGCXA4SmKkKFKMJdDyOJ2If3EB3NurbJ3fr2uEmRnT+yNdlirx43I1jIgilp3PzMy7uyX5XTww8wQwM3NPjy5Lo1HjiaoiSnWAUjZNJqtEeuJIpLmFj48jfz4P3NkBiqZ96SKlx/XLUamz8h8+aitcnbgq/Pwc2NbWtMK/JiIc1mWpUmezs0BblX2HKtjn099xdcmpE6+9cOOPiq7HV1eBoRBQ9RHxOP7v7+4wnslgvKsL46rPUNpjYgKYSmHe8jLm7e+jJWf+Zwj4ixAlSzV1pkSKxwP886uwamWV2IrFUPjx8Y/5OmyZZZb9CPsNwjbiNsNoaSUAAAAldEVYdGRhdGU6Y3JlYXRlADIwMjItMDctMjFUMTQ6Mjg6MjErMDg6MDAlTXOcAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDIyLTA3LTIxVDE0OjI4OjIxKzA4OjAwVBDLIAAAAEt0RVh0c3ZnOmJhc2UtdXJpAGZpbGU6Ly8vaG9tZS9hZG1pbi9pY29uLWZvbnQvdG1wL2ljb25fdnlibmY3eTA1di9zaGlqaWFuX28uc3ZnIRKttAAAAABJRU5ErkJggg=="
						mode=""></image>
					<text>时频谱</text>
				</view>
			</view>
		</view>

		<view class="content">
			<view class="units">单位:( {{units}} )</view>
			<view class="charts-box">
				<qiun-data-charts class="chartsed" type="line" :opts="opts" :chartData="chartData"
				 :ontouch="true"
				 	:onzoom="true" :disableScroll="true"/>
			</view>
		</view>
		<!-- 单选框 -->
		<view class="groupbox">
			<u-radio-group v-model="value" @change="radioGroupChange" size="20px">
				<u-radio label-size="15px" icon-size="5px" @change="radioChange(item.code,item.signal)"
					v-for="(item, index) in radioList" :key="index" :name="item.name" :title="item.title"
					:disabled="item.disabled" active-color="#1890FF" class="uradios">
					{{item.name}}

				</u-radio>
			</u-radio-group>
		</view>

		<u-picker mode="time" v-model="show" :params="params" @confirm="confirm"></u-picker>
	</view>
</template>

<script>
	import {
		gatHeritems,
		gatcurveDatalist
	} from "@/api/api.js"
	export default {
		data() {

			return {
				codename:'',
				dataIndex: [],
				dataValue: [],
				unitsed: '',
				units:'',
				signals: '',
				datayAxis: [],
				dataxAxis: [],
				params: {
					year: true,
					month: true,
					day: true,
					hour: true,
					minute: true,
					second: false
				},
				show: false, //时间选择器显示隐藏
				radioList: [

				],
				// u-radio-group的v-model绑定的值如果设置为某个radio的name，就会被默认选中
				value: '',
				codeed: '',
				stime: "",
				etime: "",
				currentTime: '',
				chartData: {},
				opts: {
					rotate: false,
					rotateLock: false,
					enableScroll: true,
					enableMarkLine: true,
					color: ["#1890FF"],
					dataPointShape: false,
					dataLabel: false,
					padding: [28, 60, 20, 68],
					legend: {
						show: false
					},
					xAxis: {
						splitNumber:2,
						gridEval:1,
						rotateLabel:true,
						rotateAngle:0.0001,
						fontSize:10,
						type: 'grid',
						gridType: 'dash',
						"labelCount": 5,
						disableGrid: true,
						boundaryGap:"justify",
						scrollShow: true,
						itemCount:500000,
						scrollAlign: 'bottom', //滚动条初始位置
						scrollBackgroundColor: '#262b41', //默认为 #EFEBEF
						scrollColor: '#262b41', //默认为 #A6A6A6
						dashLength: 4,
							scrollAlign:'right',
					},
					yAxis: {
						fontSize: 12,
						gridType: "dash",
						dashLength: 1,
						tofix: 2
					},
					extra: {
						line: {
							type: "straight",
							width: 1
						}
					}
				},
			};
		},
		async onLoad(options) {
			
			// #ifdef APP-PLUS
			setTimeout(() => {
			plus.screen.lockOrientation("landscape-primary");
			},200);
			// #endif
			
			
			
			this.stime = this.change_date(3);
			this.etime = this.change_date(0);
			let data = JSON.parse(options.data);
			this.data = data
			this.pointname = this.data.pointname
			await this.getitemsList(this.data.pointcode);
			console.log(this.value, '单选')
			let currentRadio = this.radioList.find(item => item.name = this.value)
			let code = currentRadio.code
			this.signals = currentRadio.signal
			this.gatDatalist(code);

		},
		onShow() {
			setTimeout(() => {
				if(this.datayAxis==''){
					uni.showLoading({
						title: '加载中'
					});
				}
			}, 800);
		
		},
		onUnload() {
			// #ifdef APP-PLUS
			plus.screen.lockOrientation('portrait-primary');
			// #endif

		},
		onReady() {
			this.getServerData();
		},
		methods: {
			change_date(days) {
				// 参数表示在当前日期下要增加的天数
				var now = new Date();
				// + 1 代表日期加，- 1代表日期减
				now.setDate((now.getDate() + 1) - 1 * days);
				var year = now.getFullYear();
				var month = now.getMonth() + 1;
				var day = now.getDate();

				var hour = now.getHours();
				var minute = now.getMinutes();
				var second = now.getSeconds();
				if (hour < 10)
					hour = '0' + hour;
				if (minute < 10)
					minute = '0' + minute;
				if (second < 10)
					second = '0' + second;
				if (month < 10) {
					month = '0' + month;
				}
				if (day < 10) {
					day = '0' + day;
				}
				return year + '-' + month + '-' + day + " " + hour + ":" + minute + ":" + second;
			},



			//点击查询
			query() {
					this.gatDatalist(this.pointId)
			},
			openDatePicker(time) {
				this.show = !this.show
				this.currentTime = time
			},
			confirm(time) {
				console.log(time)
				let {
					year: y,
					month: m,
					day: d,
					hour: h,
					minute: mm
				} = time
				// console.log(`${y}/${m}/${d} ${h}:${mm}`)
				this[this.currentTime] = `${y}/${m}/${d} ${h}:${mm}`
			},
			// 选中某个单选框时，由radio时触发
			radioChange(code, signal) {
				this.signals = signal
				let codes = this.gatDatalist(code)
			},
			// 选中任一radio时，由radio-group触发
			radioGroupChange(code) {

			},
			//单选框数据
			async getitemsList(pointcode) {
				let res = await gatHeritems(pointcode)
					console.log(res, "单选动态数据")
					if (res.statusCode == 200) {
						this.radioList = res.data
						let radioOption={
							name:'温度',
							signal:100,
							unit:'℃',
						// code: this.radioList[0].code,
							itemName:"temp"
						}
						this.radioList.push(radioOption)
						this.value = res.data[0].name
						console.log(radioOption.code,'数据1')
						
					}
				
			},

			//获取曲线数据
			gatDatalist(codes) {
				console.log(this.codename, 'signal')
				let params = {
					Origin: "app",
					RequestData: {
						// pointId:"10070010010301",
						pointId: codes,
						signalType: 0,
						startTime: this.stime,
						endTime: this.etime
					},
				}
				console.log(params.RequestData.pointId, "得到code")
				gatcurveDatalist(JSON.stringify(params)).then(res => {
					console.log(res, "10")
					if (res.statusCode == 200) {
						console.log(res, "10")
						this.datayAxis = res.data.data.map(item => {
							return (item.rms).toFixed(3)
						})
						this.dataxAxis = res.data.data.map(item => {
							return item.dtime
						})
						this.pointId = res.data.pointId
						this.unitsed = res.data.engineerUnit
						this.units=this.unitsed 
						if(this.signals==100){
							this.units='℃'
						}
						// 获取指定的数据
						this.dataIndex = res.data.data[0];
						this.dataValue = res.data.data[1];

						console.log(this.dataIndex, "codsss")
						// console.log(this.datayAxis)
						this.getServerData();

					}
				})
			},

			//ucharts-曲线图
			getServerData() {
				setTimeout(() => {
					let res = {
						categories: this.dataxAxis, //x轴

						series: [{ //y轴
							name: '单位:' + (this.unitsed) + "   " + "值",
							data: this.datayAxis
						}, ]
					};
					this.chartData = JSON.parse(JSON.stringify(res));
					// console.log(this.datayAxis,5425)
				}, 500);
			},







			//跳时频谱
			goFrequency() {
				let data = {
					pointname: this.pointname,
					pointId: this.pointId,
					signal: this.signals,
					dataIndex: this.dataIndex,
					dataValue: this.dataValue,
					unit:this.unitsed 
				}
				console.log(data.pointname, "111")
				if (data.signal == 100) {
					uni.showModal({
						title: "请选择振动趋势",
						
					})
					return
				}
				if (data.pointId == '') {
					uni.showModal({
						title: "请勾选数据类型"
					})
				}
				if (data.pointId ) {
					uni.navigateTo({
						url: `/pages/frequency/index?data=${JSON.stringify(data)}`
					})
				}
				
			},

			back() {
				uni.navigateBack({
					delta: 1
				})
			},




		}
	};
</script>

<style lang="scss" scoped>
	.charts-box {
		color: #AFDDFF;
		width: 100%;
		height: 215px;
	}

	.chartsed {
		width: 100%;

	}

	.groupbox {
		display: flex;
		align-items: center;
		justify-content: space-around;
	}

	.uradios {
		color: #007AFF;
	}

	.pages-hareder {
		width: 99%;
		height: 50px;
		background-color: #007AFF;
	}
	.units{
		color: #999999;
		height: 12px;
		font-size: 14px;
		position: relative;
		z-index: 9;
		left: 74px;
		top:10px;
	}

	@import "index.scss";
</style>
