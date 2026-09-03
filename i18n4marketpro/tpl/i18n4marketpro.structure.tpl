<!-- BEGIN: MAIN -->
<div id="ajaxBlock">
    <div class="block">
        <!-- Заголовок и предупреждения -->
        <div class="border-bottom border-secondary py-3 px-3 mb-4">
            <h2 class="mb-0">{PHP.L.i18n4marketpro_structure}</h2>
		</div>
		
        {FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}
		
        <!-- Форма перевода категорий -->
        <form action="{I18N4MARKETPRO_ACTION}" method="post">
            <div class="py-4 px-4 min-height-50vh">
                <!-- Шапка с языками -->
                <div class="row mb-4">
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-success text-white rounded">
                            {I18N4MARKETPRO_ORIGINAL_LANG}
						</div>
					</div>
                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-warning text-dark rounded">
                            {I18N4MARKETPRO_TARGET_LANG}
						</div>
					</div>
				</div>
				
                <!-- Строки категорий -->
                <!-- BEGIN: I18N4MARKETPRO_CATEGORY_ROW -->
                <div class="row mb-4 g-3">
                    <!-- Левая колонка – оригинал -->
                    <div class="col-12 col-md-6">
                        <div class="bg-success-subtle p-3 h-100 rounded">
                            <h4 class="mb-2">{I18N4MARKETPRO_CATEGORY_ROW_TITLE}</h4>
                            <em class="text-muted">{I18N4MARKETPRO_CATEGORY_ROW_DESC}</em>
                            <input type="hidden" name="{I18N4MARKETPRO_CATEGORY_ROW_CODE_NAME}" value="{I18N4MARKETPRO_CATEGORY_ROW_CODE_VALUE}" />
						</div>
					</div>
                    <!-- Правая колонка – поля перевода -->
                    <div class="col-12 col-md-6">
                        <div class="bg-warning-subtle p-3 h-100 rounded">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{PHP.L.Title}</label>
                                <input type="text" class="form-control" name="{I18N4MARKETPRO_CATEGORY_ROW_ITITLE_NAME}" value="{I18N4MARKETPRO_CATEGORY_ROW_ITITLE_VALUE}" maxlength="128" />
							</div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{PHP.L.Description}</label>
                                <textarea class="form-control" name="{I18N4MARKETPRO_CATEGORY_ROW_IDESC_NAME}" rows="4">{I18N4MARKETPRO_CATEGORY_ROW_IDESC_VALUE}</textarea>
							</div>
						</div>
					</div>
				</div>
                <!-- END: I18N4MARKETPRO_CATEGORY_ROW -->
				
                <!-- Кнопка отправки -->
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary col-12 col-md-4">{PHP.L.Update}</button>
					</div>
				</div>
			</div>
		</form>
		<div class="my-3">
			<nav aria-label="Market Pagination" class="mt-3">
				<ul class="pagination justify-content-center">{I18N4MARKETPRO_PAGINATION_PREV}{I18N4MARKETPRO_PAGNAV}{I18N4MARKETPRO_PAGINATION_NEXT}</ul>
			</nav>
		</div>
	</div>
</div>
<!-- END: MAIN -->