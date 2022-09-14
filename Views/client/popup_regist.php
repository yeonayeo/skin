<div id="popup_regist" class="popup_wrap" style="width: 910px;" v-cloak>
  <button type="button" class="popup_close" onclick="popup_regist.sunrise('closePopup');">팝업 닫기</button>
  <div class="popup_head">
    <h1 class="popup_title">고객 등록</h1>
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
                <input type="text" placeholder="고객명을 입력하세요" style="width: 250px;" v-model="name" ref="name">
              </td>
              <th class="required">연락처</th>
              <td>
                <input type="text" placeholder="연락처를 숫자만 입력하세요" style="width: 250px;" v-model="hp" ref="hp">
              </td>
            </tr>
            <tr>
              <th>생년월일</th>
              <td>
                <input type="text" placeholder="생년월일을 입력하세요 ex)19921014" style="width: 250px;" v-model="birth" ref="birth">
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
      <button type="button" class="btn c2 l" style="width: 220px;" @click="action_regist">등록하기</button>
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
        hp: null,
        birth: null,
        gender_cd: 'female',
        address: null,
        memo: null,
        special_note: null
      }
    },
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
    mounted() {},
    methods: {
      action_regist() {
        let req = {
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
          url: '/client/action_regist',
          data: req,
          success: (res) => {
            if (res.res_cd === 'OK') {
              location.href = `/client`;
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
