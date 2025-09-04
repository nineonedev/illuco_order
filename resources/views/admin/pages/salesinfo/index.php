
<?php extend('layouts.admin'); ?>
<?php section('controller', 'salesinfo') ?>
<?php section('action', 'index') ?>

<?php section('title') ?>
    사이트 정보 저장
<?php end_section() ?>

<?php section('content') ?>

<?php
/** @var \App\Domains\Communication\Entities\SalesInfo|null $info */
$info = $info ?? null;
?>

<div class="no-form-container">

    <div class="no-page-row">
        <div class="no-page-head">
            <h1 class="no-heading-sm">사이트 정보 저장</h1>
        </div>

        <form
            id="frm"
            action="<?= route('admin.salesinfo.save') ?>"
            method="post"
            class="no-form"
            enctype="multipart/form-data"
        >
            <?= csrf_field() ?>
            <?php if ($info && $info->id): ?>
                <input type="hidden" name="id" value="<?= e($info->id) ?>">
            <?php endif; ?>

            <!-- 회사 정보 -->
            <fieldset class="no-form-group">
                <legend class="no-form-base-label">회사 정보</legend>

                <div class="no-form-group">
                    <div class="no-form-control --md">
                        <label for="company_name" class="no-form-control-inner">
                            <input type="text" name="company_name" id="company_name"
                                    class="no-form-control-input"
                                    value="<?= e($info->company_name ?? '') ?>" placeholder="">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">회사명</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --lg">
                        <label for="company_address" class="no-form-control-inner">
                            <input type="text" name="company_address" id="company_address"
                                    class="no-form-control-input"
                                    value="<?= e($info->company_address ?? '') ?>" placeholder="">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">회사 주소</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --md">
                        <label for="company_tel" class="no-form-control-inner">
                            <input type="text" name="company_tel" id="company_tel"
                                   class="no-form-control-input"
                                   value="<?= e($info->company_tel ?? '') ?>" placeholder="+82 31 429 8825">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">회사 연락처</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --md">
                        <label for="company_fax" class="no-form-control-inner">
                            <input type="text" name="company_fax" id="company_fax"
                                   class="no-form-control-input"
                                   value="<?= e($info->company_fax ?? '') ?>" placeholder="+82 31 429 8826">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">회사 팩스</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --md">
                        <label for="company_email" class="no-form-control-inner">
                            <input type="email" name="company_email" id="company_email"
                                   class="no-form-control-input"
                                   value="<?= e($info->company_email ?? '') ?>" placeholder="info@illuco.co.kr">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">회사 이메일</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --md">
                        <label for="company_website" class="no-form-control-inner">
                            <input type="url" name="company_website" id="company_website"
                                   class="no-form-control-input"
                                   value="<?= e($info->company_website ?? '') ?>" placeholder="https://www.illuco.co.kr">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">웹사이트</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>
                </div>
            </fieldset>

            <span class="no-form-control-space"></span>

            <!-- 은행 정보 -->
            <fieldset class="no-form-group">
                <legend class="no-form-base-label">은행 정보</legend>

                <div class="no-form-group">
                    <div class="no-form-control --md">
                        <label for="beneficiary" class="no-form-control-inner">
                            <input type="text" name="beneficiary" id="beneficiary"
                                   class="no-form-control-input"
                                   value="<?= e($info->beneficiary ?? '') ?>" placeholder="">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">수취인(Beneficiary)</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --md">
                        <label for="bank_name" class="no-form-control-inner">
                            <input type="text" name="bank_name" id="bank_name"
                                    class="no-form-control-input"
                                    value="<?= e($info->bank_name ?? '') ?>" placeholder="">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">은행명</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --lg">
                        <label for="bank_address" class="no-form-control-inner">
                            <input type="text" name="bank_address" id="bank_address"
                                    class="no-form-control-input"
                                    value="<?= e($info->bank_address ?? '') ?>" placeholder="">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">은행 주소</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --sm">
                        <label for="swift_code" class="no-form-control-inner">
                            <input type="text" name="swift_code" id="swift_code" maxlength="11"
                                    class="no-form-control-input"
                                    value="<?= e($info->swift_code ?? '') ?>" placeholder="8~11자">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">SWIFT/BIC</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>

                    <div class="no-form-control --md">
                        <label for="account_no" class="no-form-control-inner">
                            <input type="text" name="account_no" id="account_no" maxlength="34"
                                    class="no-form-control-input"
                                    value="<?= e($info->account_no ?? '') ?>" placeholder="">
                            <fieldset class="no-form-control-label">
                                <legend class="no-form-control-text">계좌번호(IBAN 가능)</legend>
                            </fieldset>
                        </label>
                        <span class="no-form-control-space"></span>
                    </div>
                </div>
            </fieldset>

            <span class="no-form-control-space"></span>

            <!-- 비고 -->
            <fieldset class="no-form-group">
                <legend class="no-form-base-label">비고</legend>

                <div class="no-form-control --full --textarea">
                    <label for="remarks" class="no-form-control-inner">
                        <textarea name="remarks" id="remarks" rows="8" class="no-form-control-input"
                                    placeholder="FTA 관련 문구"><?= e($info->remarks ?? '') ?></textarea>
                        <fieldset class="no-form-control-label">
                            <legend class="no-form-control-text">Remarks</legend>
                        </fieldset>
                    </label>
                    <span class="no-form-control-space"></span>
                </div>
            </fieldset>

            <div class="no-form-action">
                <button type="submit" class="no-btn-primary --sm">
                    <span>저장</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php end_section() ?>
