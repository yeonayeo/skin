<div id="popup_now_send" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_now_send.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">즉시 발송</h1>
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_body regist mb40">
        <div class="row mt10">
          <label class="radio">
            <input type="radio" value="1" v-model="person_cd" :disabled="!is_all">
            <span>전체 고객</span>
          </label>
        </div>
        <div class="row mt20">
          <label class="radio">
            <input type="radio" value="2" v-model="person_cd">
            <span>고객 검색</span>
          </label>
          <div class="input_search">
            <input type="search" placeholder="고객명으로 검색하세요" style="width: 310px;" v-model="client_name" @keypress.enter="search_client">
            <button type="button" class="btn_input_search" v-model="client_name" @click="search_client"></button>
            <!-- 검색결과 -->
            <div class="search_result">
              <ul class="search_list">
                <li v-for="(client, index) in search_list" @click="selected_client(client.id, client.name)">
                  <div class="name" v-html="client.name_txt"></div>
                  <div class="phone">{{ client.hp }}</div>
                </li>
              </ul>
            </div>
            <!-- END -->
          </div>
        </div>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c1 l" style="width: 220px;" @click="action_now_send">즉시 발송</button>
      <button type="button" class="btn e2 l" style="width: 110px;" onclick="popup_now_send.sunrise('closePopup');">취소</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;
  var POPUP_GET = <?=json_encode($_GET);?>;

  var POPUP_NOW_SEND = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        get: POPUP_GET,
        req: {},
        err: {},
        id: POPUP_RES.id,
        person_cd: (POPUP_RES.is_all===true) ? 1 : 2,
        is_all: POPUP_RES.is_all,
        client_id: null,
        client_name: null,
        search_list: []
      }
    },
    mounted() {},
    methods: {
      search_client() {
        if (!this.client_name) return alert('고객명을 입력해주세요.');
        $.ajax({
          url: '/search/client',
          data: {name: this.client_name},
          success: (res) => {
            if (res.res_cd === 'OK') {
              this.search_list = res.data;
            } else {
              alert(res.err_msg);
            }
          }
        });
      },
      selected_client(id, name) {
        this.client_id = id;
        this.client_name = name;
        this.search_list = [];
      },
      action_now_send() {
        let req = {
          id: this.id,
          client_id: this.client_id,
          is_all: (this.person_cd==1) ? true : false
        };

        if(!req.is_all && !req.client_id) return alert('고객을 선택해주세요');

        $.ajax({
          url: '/setting/sms/action_now_send',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              alert('문자가 발송되었습니다.');
              location.href = `/setting/sms`;
            } else {
              alert(res.err_msg);
            }
          }
        });
      }
    }
  });

  POPUP_NOW_SEND.mount('#popup_now_send');
</script>
