<!-- BEGIN: MAIN -->
<div class="border-bottom border-secondary py-3 px-3">
	
	<nav aria-label="breadcrumb">
		<div class="ps-container-breadcrumb">
			<ol class="breadcrumb d-flex mb-0">
				{I18N4MARKETPRO_TITLE}
			</ol>
		</div>
	</nav>
	
</div>
<div class="py-4 px-4 min-height-50vh">
	<h2 class="mb-4"></h2>
	{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
	<form action="{I18N4MARKETPRO_ACTION}" method="post">
		<div class="row">
			<div class="col-12 mb-5">
				<div class="row">
					<div class="col-6">
						<div class="p-3 mb-2 bg-success text-white">{PHP.L.i18n4marketpro_original} ({I18N4MARKETPRO_ORIGINAL_LANG})</div>
					</div>
					<div class="col-6">
						<div class="p-3 mb-2 bg-warning text-dark">
							<div class="row">
								<div class="col-4">{PHP.L.i18n4marketpro_translations_items}</div>
								<div class="col-8">
									<div class="row">
										<div class="col-6">
											<div class="col-12 text-center py-2">
												{I18N4MARKETPRO_LOCALIZED_LANG}
											</div>
											
										</div>
										<div class="col-6">
											
											<div class="col-12 text-center py-2">
												<button type="submit" class="btn btn-primary w-100">{PHP.L.Submit}</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12">
				<div class="card border-0 mb-5" style="border-radius: 0;">
					<div class="card-body p-0">
						<div class="row">
							<div class="col-12 col-md-6 bg-success-subtle">
								<p class="fw-semibold mt-2">{PHP.L.Title}</p>
								<div class="mb-3 border rounded border-secondary p-2"><span class="fw-semibold">{I18N4MARKETPRO_PAGE_TITLE}</span></div>
								<p class="fw-semibold">{PHP.L.Description}</p>
								<div class="mb-3 border rounded border-secondary p-2">{I18N4MARKETPRO_PAGE_DESC}</div>
								<p class="fw-semibold mt-2">{PHP.L.market_metatitle}</p>
								<div class="mb-3 border rounded border-secondary p-2"><span class="fw-semibold">{I18N4MARKETPRO_PAGE_METATITLE}</span></div>
								<p class="fw-semibold">{PHP.L.market_metadesc}</p>
								<div class="mb-3 border rounded border-secondary p-2">{I18N4MARKETPRO_PAGE_METADESC}</div>
								<!-- IF {I18N4MARKETPRO_PAGE_TAGS} --> 
								<p class="fw-semibold">{PHP.L.Tags}</p>
								<div class="mb-3 border rounded border-secondary p-2">{I18N4MARKETPRO_PAGE_TAGS}</div>
								<!-- ENDIF -->
								<p class="fw-semibold">{PHP.L.Text}</p>
								<div class="mb-3 border rounded border-secondary p-2">{I18N4MARKETPRO_PAGE_TEXT}</div>
							</div>
							<div class="col-12 col-md-6 bg-warning-subtle">
								<div class="py-2">
									<label class="form-label"><span class="fw-semibold">{PHP.L.Title}</span></label>
									<input type="text" class="form-control" name="title" value="{I18N4MARKETPRO_IPAGE_TITLE}" maxlength="128" />
								</div>
								<div class="py-2">
									<label class="form-label"><span class="fw-semibold">{PHP.L.Description}</span></label>
									<textarea class="form-control" name="desc" maxlength="255" rows="4">{I18N4MARKETPRO_IPAGE_DESC}</textarea>
								</div>
								<!-- ===== БЛОК ДОПОЛНИТЕЛЬНЫХ ПОЛЕЙ (EXTRAFIELDS) ===== -->
								<!-- IF {I18N_PAGE_FORM_MXTRA_META_TITLE} --> 
								<div class="col-12 js-chars-limit-block">
									<label for="marketMetaTitle" class="form-label fw-semibold">{I18N_PAGE_FORM_MXTRA_META_TITLE_TITLE}</label>
									<div class="input-group has-validation">
										{I18N_PAGE_FORM_MXTRA_META_TITLE}
									</div>
									<div class="form-text text-end">
										<span class="text-muted small js-chars-counter" data-limit="55"></span>
									</div>
								</div>
								<!-- ENDIF -->
								<!-- IF {I18N_PAGE_FORM_MXTRA_META_DESCRIPTION} --> 
								<div class="col-12 js-chars-limit-block">
									<label for="marketMetaTitle" class="form-label fw-semibold">{I18N_PAGE_FORM_MXTRA_META_DESCRIPTION_TITLE}</label>
									<div class="input-group has-validation">
										{I18N_PAGE_FORM_MXTRA_META_DESCRIPTION}
									</div>
									<div class="form-text text-end">
										<span class="text-muted small js-chars-counter" data-limit="155"></span>
									</div>
								</div>
								<!-- ENDIF -->	
								<!-- IF {I18N_PAGE_FORM_MXTRA_PAGE_H1} --> 
								<div class="col-12 js-chars-limit-block">
									<label for="marketMetaTitle" class="form-label fw-semibold">{I18N_PAGE_FORM_MXTRA_PAGE_H1_TITLE}</label>
									<div class="input-group has-validation">
										{I18N_PAGE_FORM_MXTRA_PAGE_H1}
									</div>
									<div class="form-text text-end">
										<span class="text-muted small js-chars-counter" data-limit="170"></span>
									</div>
								</div>
								<!-- ENDIF -->	
								<!-- ===== КОНЕЦ БЛОКА EXTRAFIELDS ===== -->
								<!-- BEGIN: TAGS -->
								<div class="py-2">
									<label class="form-label"><span class="fw-semibold">{PHP.L.Tags}</span></label>
									{I18N4MARKETPRO_IPAGE_TAGS}
									<small class="text-muted">({PHP.L.tags_comma_separated})</small>
								</div>
								<!-- END: TAGS -->
								<div class="py-2">
									<label class="form-label"><span class="fw-semibold">{PHP.L.Text}</span></label>
									{I18N4MARKETPRO_IPAGE_TEXT}
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<!-- This is the name of the template for informing the administrator -->
<!-- IF {PHP.usr.maingrp} == 5 -->

<div class="alert alert-warning" role="alert">
	<strong>{PHP.usr.name}</strong>, This is the HTML template <code>i18n4marketpro.market.tpl</code>
</div>

<!-- ENDIF -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Для каждого блока с js-chars-limit-block
        document.querySelectorAll('.js-chars-limit-block').forEach(function(block) {
            // Найти первое поле ввода или textarea внутри этого блока
            const field = block.querySelector('input, textarea');
            // Найти счётчик внутри этого же блока
            const counter = block.querySelector('.js-chars-counter');
            if (!field || !counter) return;
            
            const limit = parseInt(counter.getAttribute('data-limit'), 10);
            
            function update() {
                let val = field.value;
                if (val.length > limit) {
                    field.value = val.substring(0, limit);
				}
                const remaining = limit - field.value.length;
                counter.textContent = 'Осталось: ' + remaining + ' симв.';
			}
            
            field.addEventListener('input', update);
            update(); // начальное значение
		});
	});
</script>
<!-- END: MAIN -->

<!-- Каждое поле выводится в цикле через блок EXTRAFLD -->
<!-- BEGIN: EXTRAFLD -->
<div class="py-2">
	<label class="form-label fw-semibold">{I18N_PAGE_FORM_EXTRAFLD_TITLE}</label>
	{I18N_PAGE_FORM_EXTRAFLD}
</div>
<!-- END: EXTRAFLD -->