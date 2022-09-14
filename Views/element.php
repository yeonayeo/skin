<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="element">
  <h2>Components</h2>
  <div class="flex_area">
    <div class="area">
      <div class="title">Buttons</div>
      <div class="row">
        <div class="row_title">button - colored</div>
        <div class="col">
          <button type="button" class="btn c1 l">btn c1 L</button>
          <button type="button" class="btn c2 l">btn c2 L</button>
          <button type="button" class="btn c3 l">btn c3 L</button>
          <button type="button" class="btn c4 l">btn c4 L</button>
        </div>
      </div>
      <div class="row">
        <div class="row_title">button - empty</div>
        <div class="col">
          <button type="button" class="btn e1 l">btn e1 L</button>
          <button type="button" class="btn e2 l">btn e2 L</button>
          <button type="button" class="btn e3 l">btn e3 L</button>
          <button type="button" class="btn e4 l">btn e4 L</button>
        </div>
      </div>
      <div class="row">
        <div class="row_title">button - size</div>
        <div class="col">
          <button type="button" class="btn c1 l">btn L</button>
          <button type="button" class="btn c1 m">btn M</button>
          <button type="button" class="btn c1 s">btn S</button>
        </div>
      </div>
      <div class="row">
        <div class="row_title">btn_regist</div>
        <div class="col">
          <button type="button" class="btn c1 l btn_regist">OO 등록</button>
        </div>
      </div>
      <div class="title mt50">Input</div>
      <div class="row">
        <div class="row_title">switch</div>
        <label class="switch">
          <input type="checkbox">
          <span></span>
        </label>
      </div>
      <div class="row">
        <div class="row_title">checkbox</div>
        <div class="col" style="min-height: 25px;">
          <label class="checkbox">
            <input type="checkbox">
            <span></span>
          </label>
          <label class="checkbox">
            <input type="checkbox" checked>
            <span></span>
          </label>
        </div>
      </div>
      <div class="row">
        <div class="row_title">radio</div>
        <div class="col">
          <label class="radio">
            <input type="radio" value="" checked>
            <span>고객 정보 검색</span>
          </label>
          <label class="radio ml10">
            <input type="radio" value="">
            <span>고객 정보 검색</span>
          </label>
        </div>
      </div>
      <div class="row">
        <div class="row_title">search</div>
        <!-- popup_contents > content_body -->
        <div class="popup_contents">
          <div class="content_body" style="margin-bottom: 125px; padding: 0; border-bottom: none;">
            <div class="input_search" style="width: 250px;">
              <input type="search" placeholder="제품명을 검색하세요" style="width: 250px;">
              <button type="button" class="btn_input_search"></button>
              <!-- 검색결과 -->
              <div class="search_result">
                <ul class="search_list">
                  <li>
                    <div class="name">김<span class="keyword">지아</span></div>
                    <div class="phone">010-****-5678</div>
                  </li>
                  <li>
                    <div class="name">김<span class="keyword">지아</span></div>
                    <div class="phone">010-****-5678</div>
                  </li>
                  <li>
                    <div class="name">김<span class="keyword">지아</span></div>
                    <div class="phone">010-****-5678</div>
                  </li>
                </ul>
              </div>
              <!-- END -->
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="row_title">select</div>
        <select style="width: 250px;">
          <option>옵션1</option>
          <option>옵션2</option>
          <option>옵션3</option>
          <option>옵션4</option>
        </select>
      </div>
    </div>
    <div class="area">
      <div class="title">Others</div>
      <div class="row">
        <div class="row_title">tag</div>
        <div class="col">
          <span class="tag c1">완료</span>
          <span class="tag e1">취소</span>
          <span class="tag c1 l">카드</span>
        </div>
      </div>
      <div class="row">
        <div class="row_title">pagination</div>
        <div class="pagination" style="margin-top: 0;">
          <div class="navi">
            <button type="button"><i class="material-icons">navigate_before</i></button>
          </div>
          <div class="pages">
            <button type="button" class="on">1</button>
            <button type="button">2</button>
            <button type="button">3</button>
            <button type="button">4</button>
            <button type="button">5</button>
            <button type="button">6</button>
            <button type="button">7</button>
            <button type="button">8</button>
            <button type="button">9</button>
            <button type="button">10</button>
          </div>
          <div class="navi">
            <button type="button"><i class="material-icons">navigate_next</i></button>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="row_title">select - toggle (custom)</div>
        <div class="col">
          <div class="select_custom" style="width: 250px;" :class="{on: option_visible}">
            <div class="select_head">
              <div class="select placeholder" @click="option_visible = !option_visible">옵션을 선택하세요</div>
              <!-- <div class="select selected" @click="option_visible = !option_visible">선택된 옵션</div> -->
            </div>
            <ul class="select_body">
              <li class="option">옵션명</li>
              <li class="option">옵션명</li>
              <li class="option">옵션명</li>
              <li class="option disabled">옵션명(disabled)</li>
              <li class="option disabled">옵션명(disabled)</li>
              <li class="option disabled">옵션명(disabled)</li>
            </ul>
          </div>
          <div class="select_custom" style="width: 250px;" :class="{on: option_visible}">
            <div class="select_head">
              <!-- <div class="select placeholder" @click="option_visible = !option_visible">옵션을 선택하세요</div> -->
              <div class="select selected" @click="option_visible = !option_visible">선택된 옵션</div>
            </div>
            <ul class="select_body">
              <li class="option">옵션명</li>
              <li class="option">옵션명</li>
              <li class="option">옵션명</li>
              <li class="option disabled">옵션명(disabled)</li>
              <li class="option disabled">옵션명(disabled)</li>
              <li class="option disabled">옵션명(disabled)</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  var FRONT = Vue.createApp({
    data() {
      return {
        res: {},
        get: {},
        option_visible: false
      }
    },
    mounted() {},
    methods: {}
  });
  FRONT.mount('#element');
</script>

<? include VIEWS_PATH.'/_include/foot.php'; ?>
