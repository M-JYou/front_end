# lsj-nlist

### 可导入示例项目查看完整demo
### 若有疑问可进QQ讨论群：701468256

## 使用说明
### 选填项
|	 属性	 | 是否必填 	|  		值类型		  |		默认值 	| 		说明						|
| ---------- | :------: | ------------------: | ----------: | ------------------------: 	|
| type		 |	否  	  	| 	 String			  |	'list' 		| 瀑布流列表传 waterfall			|
| isRefresh	 |	否  	  	| 	 Boolean		  |	true 		| 是否允许下拉刷新，默认允许		|
| refreshBackgroundColor	 |	否  	  	| 	 String		  |	无 		| 下拉刷新区域背景色		|
| refreshColor	 |	否  	  	| 	 String		  |	'#999999' 	| 下拉刷新区域字体色				|
| init		 |	否  	  	| 	 Boolean		  |	true 		| 初始化时执行一次刷新，为false时<br/> 需手动执行this.$refs.lsjNlist.create()			|
| downText	 |	否 	  	| 	 Object			  |	下拉刷新提示	| <a href="#downText">downText说明</a>										|
| upText	 |	否 	  	| 	 Object			  |	上拉加载提示	| <a href="#upText">upText说明</a>											|
| position	 |	否 	  	| 	 String			  |	'fixed'		| 组件定位模式，默认fixed			|
| unit		 |	否 	  	| 	 String			  |	无			| 上下左右布局单位，可选rpx、px,设置后上下左右不能单独设置						|
| topUnit	 |	否 	  	| 	 String			  |	'rpx'		| 上方的单位						|
| bottomUnit |	否 	  	| 	 String			  |	'rpx'		| 下方的单位						|
| aboutUnit  |	否 	  	| 	 String			  |	'rpx'		| 左右的单位						|
| top、bottom<br/>left、right |	否 	 |  Number|	0			| 上下左右布局					|
| columnCount|	否 	  	| 	 Number			  |	2			| 仅瀑布流有效：描述瀑布流的列数												|
| columnWidth|	否 	  	| 	 [Number,String]  |	'auto'		| 仅瀑布流有效：描述瀑布流每一列的列宽											|
| columnGap	 |	否 	  	| 	 Number			  |	0			| 仅瀑布流有效：列与列之间的间隙												|
| leftGap	 |	否 	  	| 	 Number			  |	0			| 仅瀑布流有效：列与左边cell的间隙												|
| rightGap	 |	否 	  	| 	 Number			  |	0			| 仅瀑布流有效：列与右边cell的间隙												|

### 必填项
| 回调函数	 | 是否必填 	|  		值类型		  |		默认值 	| 		说明						|
| ---------- | :------: | ------------------: | ----------: | ------------------------: 	|
| loadData	 |	是 	  	| 	 Function		  |	-			| 功能触发回调执行函数							|

<h2 id="downText">downText说明</h2>

|	 属性	 	| 		默认值 	| 		说明						|
| ------------- | ------------: | ------------------------: 	|
| contentdown	|	下拉可以刷新 | 手指下拉开始时顶部显示			|
| contentover	|	释放立即刷新 | 手指下拉到指定位置时顶部显示		|
| contentrefresh|	正在刷新...	| 数据请求中						|
| contentnomore|	刷新完成		| 下拉刷新执行完毕时显示			|

<h2 id="upText">upText说明</h2>

|	 属性	 	| 		默认值 	| 		说明						|
| ------------- | ------------: | ------------------------: 	|
| contentdown	|	上拉加载更多 | isMore为true时显示(触底时自动执行下次请求)	|
| contentrefresh|	正在加载...	| 数据请求中									|
| contentnomore	|没有更多数据了	| isMore为false时显示(触底时不再执行请求)		|

# 示例

### vue:
``` javascript
<lsj-nlist ref="lsjNlist" type="list" init @loadData="loadData">
	<!-- 此处可写cell、header -->
	<cell v-for="(item,index) in list" :key="index">
		<!-- 此处写一列的内容 -->
		
	</cell>
</lsj-nlist>
```

### js：
``` javascript
data() {
	return {
		top: 0,
		list: [],
		fromData: {
			pageNum: 1,
			pageSize: 20
		}
	}
}
methods: {
	async loadData(refresh) {
		// 下拉刷新重置请求参数
		if (refresh) {this.fromData.pageNum = 1;}
		
		// start:----模拟接口请求-----
		let promise = new Promise((resolve, reject) => {setTimeout(() => resolve('仿接口请求'), 1000)})
		let res = await promise;
		console.log(refresh,res,this.fromData);
		// end:----模拟部分需删除-----
		
		// 下拉刷新请求到数据再置空之前的数据，避免出现空白页
		if (refresh) {
			this.list = [];
		}
		
		// start:----模拟加载列表-----
		for (let i=0,len=39;i<len;i++) {this.list.push({name:i});}
		// end:----模拟部分需删除-----
		
		let isMore = this.list.length <= 100;
		// 如果还有下一页，则下次请求page+1;
		if (isMore) {
			this.fromData.pageNum++;
		}
		// 结束刷新效果
		// isMore决定是否还可以继续上拉加载更多数据
		this.$refs.lsjNlist.endLoad(refresh,isMore);
	}
}
```


