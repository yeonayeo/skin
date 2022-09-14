<div id="popup_detail" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_detail.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">직원 정보</h1>
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_head">
        <div class="content_title">직원 정보</div>
        <button type="button" class="btn_update_book" @click="popup_update()">직원 정보 수정</button>
      </div>
      <div class="content_body">
        <table>
          <tbody>
            <tr>
              <th>직원명</th>
              <td>
                <div>{{name}}<em class="icon_master" v-if="is_super"></em></div>
              </td>
            </tr>
            <tr>
              <th>구분</th>
              <td>{{position}}</td>
            </tr>
            <tr>
              <th>연락처</th>
              <td>{{hp}}</td>
            </tr>
            <tr>
              <th>로그인 코드</th>
              <td>{{login_cd}}</td>
            </tr>
            <tr>
              <th>비고</th>
              <td>{{note}}</td>
            </tr>
            <tr>
              <th>근무 형태</th>
              <td>{{work_form}}</td>
            </tr>
            <tr>
              <th>근무 시간</th>
              <td>{{work_time}}</td>
            </tr>
            <tr>
              <th>급여 형태</th>
              <td><span style="margin-right: 10px; color: #c38370;">{{pay_form}}</span>{{pay_money}}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn e1 l" style="width: 220px;" onclick="popup_detail.sunrise('closePopup');">닫기</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;

  var POPUP_DETAIL = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        req: {},
        err: {},

        id: POPUP_RES.id,
        name: POPUP_RES.name,
        position: POPUP_RES.position,
        hp: POPUP_RES.hp,
        login_cd: POPUP_RES.login_cd,
        note: POPUP_RES.note,
        work_form: POPUP_RES.work_form,
        work_time:POPUP_RES.work_time,
        pay_form: POPUP_RES.pay_form,
        pay_money: POPUP_RES.pay_money,
        is_super: POPUP_RES.is_super
      }
    },
    mounted() {},
    methods: {
      popup_update() {
        popup_update = sunrise({
          target: '/setting/admin/popup_update?id='+this.id
        })
      }
    }
  });

  POPUP_DETAIL.mount('#popup_detail');
</script>
