<div id="popup_update" class="popup_wrap" style="width: 600px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_update.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">직원 정보 수정</h1>
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
                  <button type="button" class="btn e2 s ml10" style="width: 100px;" v-if="!is_super" @click="action_delete">코드 삭제</button>
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
      <button type="button" class="btn c1 l" style="width: 220px;" @click="action_update">수정 완료</button>
      <button type="button" class="btn e1 l" style="width: 110px;" onclick="popup_update.sunrise('closePopup');">닫기</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;
  var POPUP_GET = <?=json_encode($_GET);?>;

  var POPUP_UPDATE = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        get: POPUP_GET,
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
        pay_form_cd: (POPUP_RES.pay_form_cd) ? POPUP_RES.pay_form_cd : 1,
        pay_money: POPUP_RES.pay_money,
        is_super: POPUP_RES.is_super
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
      action_update() {
        let req = {
          id: this.id,
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
          url: '/setting/admin/action_update',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              location.href = `/setting/admin`;
            } else {
              alert(res.err_msg);
            }
          }
        });
      },
      action_delete() {
        if(!confirm('직원 정보 삭제시 복구할 수 없습니다.\n정말 삭제하시겠습니까?')) return;
        $.ajax({
          url: '/setting/admin/action_delete',
          data: { id: this.id },
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

  POPUP_UPDATE.mount('#popup_update');
</script>
