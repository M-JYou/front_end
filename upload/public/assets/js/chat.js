/** @type {(param:any)=>boolean} */
function isJSON(str) {
  if (typeof str === "string") {
    try {
      const obj = JSON.parse(str);
      if (typeof obj === "object" && obj) {
        return true;
      }
    } catch (e) {
    }
  }
  return false;
}

/** @type {(this: WebSocket, ev: Event) => any} */
function _onopen() {
  this.send(JSON.stringify({
    controller: "Connect",
    action: "index",
    args: {
      // @ts-ignore
      "token": token[this.i]
    },
  }));
}
/** @type {(this: WebSocket, ev: MessageEvent<any>) => any} */
function _onmessage(e) {
  if (isJSON(e.data)) {
    const data = JSON.parse(e.data);
    //当消息返回错误信息时
    if (data.error !== undefined) {
      // @ts-ignore
      console.log(this.i + "错误信息", data);
      return false;
    }
    //当消息返回黑名单限制时
    if (data.type == "isInBlacklist") {
      // @ts-ignore
      console.log(this.i + "黑名单", data);
      return false;
    }
    // @ts-ignore
    console.log(this.i + "收到数据", data);
  } else {
    // @ts-ignore
    if (e.data != 'PONG') console.log(this.i + "client：", e.data);
  }
}
/** @type {(this: WebSocket, ev: Event) => any} */
function _onerror() {
  // 连接建立失败重连
  // @ts-ignore
  console.log(this.i + "client：重新连接websocket，正在尝试第" + (++recon) + "次");
  if (recon >= 5) {
    // @ts-ignore
    console.log(this.i + "client：连接websocket失败，请刷新页面重试");
    return false;
  }
  // @ts-ignore
  initWebSocket(this.i);
}
/** @type {(this: WebSocket, ev: CloseEvent) => any} */
function _onclose(ev) {
  // @ts-ignore
  console.log(this.i + "client：关闭连接", ev);
}

const token = [
  // 企业
  "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2NjYwMTEwMDcsInN1YiI6IuS4u-mimCIsIm5iZiI6MTY2NTkyNDkwNywiYXVkIjoiNjMwMTAxMDA1IiwiaWF0IjoxNjY1OTI0NjA3LCJqdGkiOiIxNDgwZDFmNGExNDkwNzNkNjQ3NWEyOTJiOWY0MTY0MyIsImlzcyI6Ijc0Y21zIiwic3RhdHVzIjoxLCJkYXRhIjp7ImFwcGluZm8iOnsiaWQiOjc0MCwiYXBwa2V5IjoiVVhja0l0d1BxZ2thQmg1NCIsImFwcHNlY3JldCI6InFSRG04R0pSV05WRlRqZ3kiLCJkZWFkbGluZSI6MTY5NTQ1MjczNCwiZG9tYWluIjoiaHR0cDovL3Rlc3QueXpiMTY4LmNvbSJ9LCJ1c2VyaW5mbyI6eyJ1aWQiOiI2MzAxMDEwMDUiLCJ1dHlwZSI6IjEiLCJyZXN1bWVpZCI6IjAiLCJhdmF0YXIiOiIvdXBsb2FkL3Jlc291cmNlL2VtcHR5X2xvZ28uanBnIn19fQ.yKrhbA-_k9caWt35Q71aPvMQrRJ1FNeeGQj2xxAU_ms",
  // 个人
  "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2NjYwMTE4MjYsInN1YiI6IuS4u-mimCIsIm5iZiI6MTY2NTkyNTcyNiwiYXVkIjoiNjMwMTAxMDAzIiwiaWF0IjoxNjY1OTI1NDI2LCJqdGkiOiI3ODU4NDcyZGVlZDA5NjY0MjdjNDc1MTRlZTAwYWQ2NSIsImlzcyI6Ijc0Y21zIiwic3RhdHVzIjoxLCJkYXRhIjp7ImFwcGluZm8iOnsiaWQiOjc0MCwiYXBwa2V5IjoiVVhja0l0d1BxZ2thQmg1NCIsImFwcHNlY3JldCI6InFSRG04R0pSV05WRlRqZ3kiLCJkZWFkbGluZSI6MTY5NTQ1MjczNCwiZG9tYWluIjoiaHR0cDovL3Rlc3QueXpiMTY4LmNvbSJ9LCJ1c2VyaW5mbyI6eyJ1aWQiOiI2MzAxMDEwMDMiLCJ1dHlwZSI6IjIiLCJyZXN1bWVpZCI6IjIiLCJhdmF0YXIiOiJodHRwOi8vdGVzdC55emIxNjguY29tL3VwbG9hZC9maWxlcy8yMDIyMTAxNC84Mzk5N2U0YTM3YTkzY2YxNzhlZGRmMDNlNGIxMDU4Yy5wbmcifX19.wEN15nCcouBZviS0M7fQ7ad8-IBVGt9n4Hlrw6m8z_Y"
];
let recon = 0, /** @type {WebSocket[]} */ws = [];

function initWebSocket(p) {
  const t = new WebSocket("wss://imserv.v2.74cms.com");
  // @ts-ignore
  t.i = p ? 1 : 0;
  t.onopen = _onopen;
  t.onmessage = _onmessage;
  t.onerror = _onerror;
  t.onclose = _onclose;
  // @ts-ignore
  ws[t.i] = t;
}
function send(/** @type {any} */param, i = 0) {
  i = i ? 1 : 0;
  param.args.token = token[i];
  ws[i].send(JSON.stringify(param));
}

// @ts-ignore
$(() => {
  initWebSocket();
  initWebSocket(1);
  setInterval(() => {
    send({ controller: "Ping", action: "index", args: {} });
    send({ controller: "Ping", action: "index", args: {} }, 1);
  }, 30000);
});