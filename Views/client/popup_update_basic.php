<div id="popup_update_basic" class="popup_wrap" style="width: 910px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_update_basic.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">기본 정보 수정</h1>
  </div>
  <div class="popup_contents">
    <div class="area">
      <div class="content_head">
        <div class="content_title">기본 정보</div>
      </div>
      <div class="content_body mb40">
        <table>
          <tbody>
            <tr>
              <th class="required">고객명</th>
              <td>
                <input type="text" placeholder="고객명을 입력하세요" style="width: 250px;" v-model="name">
              </td>
              <th class="required">연락처</th>
              <td>
                <input type="text" placeholder="연락처를 숫자만 입력하세요" style="width: 250px;" v-model="hp">
              </td>
            </tr>
            <tr>
              <th>생년월일</th>
              <td>
                <input type="text" placeholder="생년월일을 숫자만 입력하세요" style="width: 250px;" v-model="birth">
              </td>
              <th class="required">성별</th>
              <td>
                <div class="flex_area ai_c fz14">
                  <label class="radio">
                    <input type="radio" value="female" v-model="gender_cd">
                    <span>여성</span>
                  </label>
                  <label class="radio ml30">
                    <input type="radio" value="male" v-model="gender_cd">
                    <span>남성</span>
                  </label>
                </div>
              </td>
            </tr>
            <tr>
              <th>주소</th>
              <td colspan="3">
                <input type="text" placeholder="주소를 입력하세요" style="width: 400px;" v-model="address">
              </td>
            </tr>
            <tr>
              <th>메모</th>
              <td colspan="3">
                <input type="text" placeholder="메모를 입력하세요" style="width: 400px;" v-model="memo">
              </td>
            </tr>
            <tr>
              <th>특이사항</th>
              <td colspan="3">
                <input type="text" placeholder="고객 특이사항을 입력하세요" style="width: 400px;" v-model="special_note">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="btn_area">
      <button type="button" class="btn c1 l" style="width: 220px;" @click="action_update">수정 완료</button>
      <button type="button" class="btn e2 l" style="width: 110px;" onclick="popup_update_basic.sunrise('closePopup');">취소</button>
    </div>
  </div>
</div>

<script>
  var POPUP_RES = <?=json_encode($_RES);?>;
  var POPUP_GET = <?=json_encode($_GET);?>;

  var POPUP_UPDATE_BASIC = Vue.createApp({
    data() {
      return {
        res: POPUP_RES,
        get: POPUP_GET,
        req: {},
        err: {},

        id: POPUP_RES.id,
        name: POPUP_RES.name,
        hp: POPUP_RES.hp,
        birth: POPUP_RES.birth,
        gender_cd: POPUP_RES.gender,
        address: POPUP_RES.address,
        memo: POPUP_RES.memo,
        special_note: POPUP_RES.special_note
      }
    },
    mounted() {},
    watch: {
      hp(n) {
        if (n) {
          this.hp = this.hp.replaceAll(/[^0-9]/g, '');
        }
      },
      birth(n) {
        if (n) {
          this.birth = this.birth.replaceAll(/[^0-9]/g, '');
        }
      }
    },
    methods: {
      action_update() {
        let req = {
          id: this.id,
          name: this.name,
          hp: this.hp,
          birth: this.birth,
          gender_cd: this.gender_cd,
          address: this.address,
          memo: this.memo,
          special_note: this.special_note
        }
        if (!req.name) return alert('고객명을 입력하세요');
        if (!req.hp) return alert('연락처를 입력해주세요');
        if (!req.gender_cd) return alert('성별을 선택해주세요');
        if (req.birth && req.birth.length!=8) {
          alert('생년월일은 8글자 입력해주세요.');
          return;
        }

        $.ajax({
          url: '/client/action_update',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              location.href = `/client/detail?id=`+this.id;
            } else {
              alert(res.err_msg);
            }
          }
        });
      }
    }
  });

  POPUP_UPDATE_BASIC.mount('#popup_update_basic');
</script>
