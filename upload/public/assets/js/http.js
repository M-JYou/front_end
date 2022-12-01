// @ts-nocheck
var service = axios.create({
  baseURL: qscms.apiUrl,
  withCredentials: true, // 跨域支持发送cookie
  timeout: 5000 // 请求超时时间
}),
  serviceBase = axios.create({
    baseURL: qscms.apiUrlBase,
    withCredentials: true, // 跨域支持发送cookie
    timeout: 5000 // 请求超时时间
  });
function then(res) {

}
function httpget(url, params, base) {
  return new Promise(function (resolve, reject) {
    (base ? serviceBase : service)
      .get(url, {
        headers: {
          'user-token': qscms.userToken,
          platform: qscms.platform,
          subsiteid: Cookies.get('qscms_subsiteid')
        },
        params: params
      })
      .then(function (res) {
        if (res.data.code > 200 || res.data.code < 200) {
          handlerHttpError(res.data);
          reject(res.data);
        } else {
          try { res.data = JSON.parse(res.data); } catch (error) { }
          resolve(res.data);
        }
      })
      .catch(function (err) {
        if (err.message.includes('timeout')) {
          window.ELEMENT.Message.error('请求超时，请刷新页面再试');
        }
        reject(err);
      });
  });
}
function httppost(url, data, base) {
  return new Promise(function (resolve, reject) {
    (base ? serviceBase : service)
      .post(url, data, {
        headers: {
          'user-token': qscms.userToken,
          platform: qscms.platform,
          subsiteid: Cookies.get('qscms_subsiteid')
        }
      })
      .then(function (res) {
        if (res.data.code > 200 || res.data.code < 200) {
          handlerHttpError(res.data);
          reject(res.data);
        } else {
          try { res.data = JSON.parse(res.data); } catch (error) { }
          resolve(res.data);
        }
      })
      .catch(function (err) {
        if (err.message.includes('timeout')) {
          window.ELEMENT.Message.error('请求超时，请刷新页面再试');
        }
        reject(err);
      });
  });
}
function postFormData(url, params,base) {
  return new Promise(function (resolve, reject) {
    (base ? serviceBase : service)({
      headers: {
        'Content-Type': 'multipart/form-data', // ;boundary=----WebKitFormBoundaryQ6d2Qh69dv9wad2u,
        'user-token': qscms.userToken,
        platform: qscms.platform,
        subsiteid: Cookies.get('qscms_subsiteid')
      },
      transformRequest: [
        function (data) {
          // 在请求之前对data传参进行格式转换
          var formData = new FormData();
          Object.keys(data).forEach(function (key) {
            formData.append(key, data[key]);
          });
          return formData;
        }
      ],
      url: url,
      method: 'post',
      data: params
    })
      .then(function (res) {
        if (res.data.code > 200 || res.data.code < 200) {
          handlerHttpError(res.data);
          reject(res.data);
        } else {
          try { res.data = JSON.parse(res.data); } catch (error) { }
          resolve(res.data);
        }
      })
      .catch(function (err) {
        if (err.message.includes('timeout')) {
          window.ELEMENT.Message.error('请求超时，请刷新页面再试');
        }
        reject(err);
      });
  });
}
