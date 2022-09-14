<div id="popup_regist" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_regist.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">직원 정보 등록</h1>
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_head">
        <div class="content_title">직원 정보</div>
      </div>
      <div class="content_body mb40">
        <table>
          <tbody>
            <tr>
              <th class="required">직원명</th>
              <td>
                <input type="text" placeholder="직원명을 입력하세요" v-model="name">
              </td>
            </tr>
            <tr>
              <th class="required">구분</th>
              <td>
                <input type="text" placeholder="구분을 입력하세요" v-model="position">
              </td>
            </tr>
            <tr>
              <th class="required">연락처</th>
              <td>
                <input type="text" placeholder="연락처를 입력하세요" v-model="hp">
              </td>
            </tr>
            <tr>
              <th>로그인 코드</th>
              <td>
                <div class="flex_row">
                  <input type="text" v-model="login_cd" disabled>
                </div>
              </td>
            </tr>
            <tr>
              <th>비고</th>
              <td>
                <input type="text" placeholder="비고를 입력하세요" v-model="note">
              </td>
            </tr>
            <tr>
              <th>근무 형태</th>
              <td>
                <input type="text" placeholder="근무 형태를 입력하세요" v-model="work_form">
              </td>
            </tr>
            <tr>
              <th>근무 시간</th>
              <td>
                <input type="text" placeholder="근무 시간을 입력하세요" v-model="work_time">
              </td>
            </tr>
            <tr>
              <th>급여 형태</th>
              <td>
                <div class="flex_row">
                  <select style="width: 120px;" v-model="pay_form_cd">
                    <option value="1">시급</option>
                    <option value="2">일급</option>
                    <option value="3">주급</option>
                    <option value="4">월급</option>
                    <option value="99">기타</option>
                  </select>
                  <input type="number" style="width: 240px;" placeholder="숫자만 입력하세요" v-model="pay_money">
                  <span class="span_txt">원</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c2 l" style="width: 220px;" @click="action_regist">정보 등록</button>
      <button type="button" class="btn e2 l" style="width: 110px;" onclick="popup_regist.sunrise('closePopup');">취소</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;
  var POPUP_GET = <?=json_encode($_GET);?>;

  var POPUP_REGIST = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        get: POPUP_GET,
        req: {},
        err: {},
        name: null,
        position: null,
        hp: null,
        login_cd: POPUP_RES.login_cd,
        note: null,
        work_form: null,
        work_time: null,
        pay_form_cd: 1,
        pay_money: null
      }
    },
    mounted() {},
    watch: {
      hp(n) {
        if (n) {
          this.hp = this.hp.replaceAll(/[^0-9]/g, '');
        }
      }
    },
    methods: {
      action_regist() {
        let req = {
          name: this.name,
          position: this.position,
          hp: this.hp,
          login_cd: this.login_cd,
          note: this.note,
          work_form: this.work_form,
          work_time: this.work_time,
          pay_form_cd: this.pay_form_cd,
          pay_money: this.pay_money
        }

        if (!req.name) return alert('직원명 입력하세요');
        if (!req.position) return alert('구분을 입력해주세요');
        if (!req.hp) return alert('연락처를 입력해주세요');
        if (req.pay_money<=0 || req.pay_money==undefined) {
          req.pay_form_cd = null;
        }

        $.ajax({
          url: '/setting/admin/action_regist',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              location.href = `/setting/admin`;
            } else {
              alert(res.err_msg);
            }
          }
        });
      }
    }
  });

  POPUP_REGIST.mount('#popup_regist');
</script>
