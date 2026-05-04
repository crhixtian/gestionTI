<div class="d-flex justify-content-between align-items-center mb-3">
	<div>
		<h3 class="mb-0">Pedido de Compra: <?php echo htmlspecialchars($requerimiento['NroPedidoCompra']); ?></h3>
		<span id="estado-requerimiento-badge">
			<?php if ((int) $requerimiento['Estado'] === 1): ?>
				<span class="badge bg-success-lt">Completo</span>
			<?php else: ?>
				<span class="badge bg-warning-lt text-dark">Pendiente</span>
			<?php endif; ?>
		</span>
	</div>
	<div class="d-flex gap-2" id="estado-requerimiento-acciones">
		<?php if ((int) $requerimiento['Estado'] === 0): ?>
			<button class="btn btn-success" onclick="marcarComoCompleto()">Marcar como Completo</button>
		<?php else: ?>
			<button class="btn btn-warning" onclick="marcarComoPendiente()">Marcar como Pendiente</button>
		<?php endif; ?>
	</div>
</div>

<div class="card mb-3">
	<div class="card-body">
		<div class="row">
			<div class="col-md-6">
				<div class="mb-2">
					<span class="fw-semibold text-secondary">Centro:</span>
					<?php echo htmlspecialchars($requerimiento['NombreCentroCosto']); ?>
				</div>
				<div class="mb-2">
					<span class="fw-semibold text-secondary">Código Meta:</span>
					<?php echo htmlspecialchars((string) ($requerimiento['CodigoMeta'] ?? '')); ?>
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-2">
					<span class="fw-semibold text-secondary">Año:</span>
					<?php echo (int) $requerimiento['Anio']; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
	<h4 class="mb-0">Ítems Registrados</h4>
	<button
		id="btn-agregar-item"
		class="btn btn-primary"
		type="button"
		data-bs-toggle="modal"
		data-bs-target="#modal-detalle"
		onclick="nuevoDetalle()">Agregar Ítem</button>
</div>

<div class="table-responsive">
	<table class="table table-vcenter card-table table-striped">
		<thead>
			<tr>
				<th>Códico SIGA</th>
				<th>Clasificador</th>
				<th>Descripción</th>
				<th>Cantidad</th>
				<th>Tecnología</th>
				<th class="text-end">Acciones</th>
			</tr>
		</thead>
		<tbody id="tabla-detalles">
			<?php if (!empty($detalles)): ?>
				<?php foreach ($detalles as $detalle): ?>
					<tr
						data-id="<?php echo (int) $detalle['Id']; ?>"
						data-id-catalogo-tecnologico="<?php echo (int) $detalle['IdCatalogoTecnologico']; ?>"
						data-codigo-tecnologia="<?php echo htmlspecialchars((string) ($detalle['CodigoTecnologia'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
						data-codigo-siga="<?php echo htmlspecialchars((string) $detalle['CodigoSiga'], ENT_QUOTES, 'UTF-8'); ?>"
						data-clasificador="<?php echo htmlspecialchars((string) ($detalle['Clasificador'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
						data-descripcion-detallada="<?php echo htmlspecialchars((string) $detalle['DescripcionDetallada'], ENT_QUOTES, 'UTF-8'); ?>"
						data-cantidad="<?php echo (int) $detalle['Cantidad']; ?>"
						data-unidad-medida="<?php echo htmlspecialchars((string) $detalle['UnidadMedida'], ENT_QUOTES, 'UTF-8'); ?>">
						<td><?php echo htmlspecialchars($detalle['CodigoSiga']); ?></td>
						<td><?php echo htmlspecialchars((string) ($detalle['Clasificador'] ?? '')); ?></td>
						<td><?php echo htmlspecialchars($detalle['DescripcionDetallada']); ?></td>
					<td><?php echo (int) $detalle['Cantidad']; ?></td>
					<td>
						<button type="button"
							class="btn btn-sm btn-outline-secondary"
							onclick="abrirDistribucionDetalle(<?php echo (int) $detalle['Id']; ?>)">
							Distribuir
						</button>
					</td>
					<td><?php echo htmlspecialchars((string) ($detalle['CodigoTecnologia'] ?? '')); ?></td>
						<td class="text-end align-middle">
							<div class="btn-group" role="group">
								<button type="button"
									class="btn btn-icon btn-lg"
									title="Editar"
									onclick="editarDetalle(<?= (int)$detalle['Id'] ?>)">
									<i class="ti ti-edit fs-2"></i>
								</button>
								<button type="button"
									class="btn btn-icon btn-lg text-danger"
									title="Eliminar"
									onclick="eliminarDetalle(<?= (int)$detalle['Id'] ?>)">
									<i class="ti ti-trash fs-2"></i>
								</button>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="7" class="text-center text-secondary">No hay ítems registrados.</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>

<div class="mt-3">
	<button class="btn btn-secondary" onclick="volver()">Volver</button>
</div>

<style>
	/* Oculta los spinners en el campo numérico de cantidad. */
	#detalle-Cantidad::-webkit-outer-spin-button,
	#detalle-Cantidad::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	#detalle-Cantidad {
		appearance: textfield;
		-moz-appearance: textfield;
	}
</style>

<!-- Modal Nuevo/Editar Detalle -->
<div class="modal modal-blur fade" id="modal-detalle" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-detalle-title">
					Pedido de Compra <?php echo htmlspecialchars($requerimiento['NroPedidoCompra']); ?>
					<?php if (!empty($requerimiento['CodigoMeta'])): ?> - Meta <?php echo htmlspecialchars((string) $requerimiento['CodigoMeta']); ?><?php endif; ?>
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form-detalle" method="post" action="index.php?module=adquisiciones&action=guardarDetalleForm">
				<input type="hidden" name="Id" id="detalle-Id">
				<input type="hidden" name="IdRequerimiento" id="detalle-IdRequerimiento" value="<?php echo (int) $requerimiento['Id']; ?>">
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label">Código SIGA</label>
						<input type="text" name="CodigoSiga" id="detalle-CodigoSiga" class="form-control" required maxlength="12">
					</div>
					<div class="mb-3">
						<label class="form-label">Clasificador</label>
						<input type="text" name="Clasificador" id="detalle-Clasificador" class="form-control" maxlength="12">
					</div>
					<div class="mb-3">
						<label class="form-label">Descripción Detallada</label>
						<textarea name="DescripcionDetallada" id="detalle-DescripcionDetallada" class="form-control" rows="3" required maxlength="200"></textarea>
					</div>
					<div class="mb-3">
						<label class="form-label">Cantidad</label>
						<input type="number" name="Cantidad" id="detalle-Cantidad" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Unidad de Medida</label>
						<input type="text" name="UnidadMedida" id="detalle-UnidadMedida" class="form-control" placeholder="UNIDAD" required maxlength="10" value="UNIDAD">
					</div>
					<div class="mb-0">
						<label class="form-label">Homologar a Catálogo</label>
						<select name="IdCatalogoTecnologico" id="detalle-IdCatalogoTecnologico" class="form-select" required>
							<option value="">Seleccione una tecnología</option>
							<?php foreach ($catalogoOpciones as $opcion): ?>
								<option value="<?php echo (int) $opcion['Id']; ?>" data-codigo="<?php echo htmlspecialchars((string) $opcion['Codigo'], ENT_QUOTES, 'UTF-8'); ?>">
									<?php echo htmlspecialchars($opcion['Codigo'] . ' - ' . $opcion['NombreGenerico']); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Guardar Ítem</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-distribucion-detalle" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-distribucion-detalle-title">Distribución por Ítem</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form-distribucion-detalle" method="post">
				<input type="hidden" name="Id" id="distribucion-Id">
				<input type="hidden" name="IdDetalleRequerimiento" id="distribucion-IdDetalleRequerimiento">
				<div class="modal-body">
					<div class="mb-3">
						<strong id="distribucion-item-info"></strong>
					</div>
					<div class="row g-3">
						<div class="col-md-5">
							<label class="form-label">Centro de Costo</label>
							<select name="IdCentroCosto" id="distribucion-IdCentroCosto" class="form-select" required>
								<option value="">Seleccione un centro</option>
								<?php foreach ($centrosCostoDistribucion as $centro): ?>
									<option value="<?php echo (int) $centro['Id']; ?>"><?php echo htmlspecialchars($centro['Siglas'] . ' - ' . $centro['NombreCentroCosto']); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-5">
							<label class="form-label">Subcentro de Costo</label>
							<select name="IdSubCentroCosto" id="distribucion-IdSubCentroCosto" class="form-select">
								<option value="">Sin subcentro</option>
							</select>
						</div>
						<div class="col-md-2">
							<label class="form-label">Cantidad</label>
							<input type="number" name="Cantidad" id="distribucion-Cantidad" class="form-control" min="1" required>
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-12 col-md-4">
							<div class="mb-1"><strong>Total solicitado:</strong> <span id="distribucion-total-solicitado">0</span></div>
							<div class="mb-1"><strong>Total distribuido:</strong> <span id="distribucion-total-distribuido">0</span></div>
							<div class="mb-1"><strong>Saldo restante:</strong> <span id="distribucion-total-saldo">0</span></div>
						</div>
						<div class="col-12 col-md-8">
							<div class="table-responsive">
								<table class="table table-sm table-striped">
									<thead>
										<tr>
											<th>Centro</th>
											<th>Subcentro</th>
											<th>Cantidad</th>
											<th class="text-end">Acciones</th>
										</tr>
									</thead>
									<tbody id="tabla-distribucion-detalle"></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary">Guardar distribución</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	const idRequerimiento = <?php echo (int) $requerimiento['Id']; ?>;
	const nroPedidoCompra = <?php echo json_encode((string) $requerimiento['NroPedidoCompra']); ?>;
	const codigoMetaRequerimiento = <?php echo json_encode((string) ($requerimiento['CodigoMeta'] ?? '')); ?>;
	const subCentrosCostoDistribucion = <?php echo json_encode($subCentrosCostoDistribucion); ?>;
	let modoEdicion = false;
	let estadoActualRequerimiento = <?php echo (int) $requerimiento['Estado']; ?>;

	function descripcionPedidoConMeta() {
		if (codigoMetaRequerimiento) {
			return 'Pedido de Compra ' + nroPedidoCompra + ' - Meta ' + codigoMetaRequerimiento;
		}

		return 'Pedido de Compra ' + nroPedidoCompra;
	}

	function setValue(id, value) {
		const el = document.getElementById(id);
		if (el) {
			el.value = value;
		}
	}

	function setText(id, text) {
		const el = document.getElementById(id);
		if (el) {
			el.textContent = text;
		}
	}

	function escapeHtml(texto) {
		return String(texto)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}

	function postForm(action, formData) {
		return fetch('index.php?module=adquisiciones&action=' + action, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			body: new URLSearchParams(formData).toString()
		}).then(function(response) {
			return response.text().then(function(text) {
				if (!response.ok) {
					console.error('HTTP error en postForm:', response.status, response.url, text);
					return { success: false, message: 'Error de servidor (' + response.status + ')' };
				}
				try {
					return JSON.parse(text);
				} catch (error) {
					console.error('JSON inválido en postForm:', error, response.url, text);
					return { success: false, message: 'Respuesta inválida del servidor', response: text };
				}
			});
		}).catch(function(error) {
			console.error('Error de red en postForm:', error);
			return { success: false, message: 'Error de red' };
		});
	}

	function postData(action, dataObject) {
		const formData = new FormData();
		Object.keys(dataObject).forEach(function(key) {
			formData.append(key, dataObject[key]);
		});
		return postForm(action, formData);
	}

	function getBootstrapModalInstance(modalEl) {
		if (!modalEl || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
			return null;
		}
		return bootstrap.Modal.getOrCreateInstance(modalEl);
	}

	function showModalById(modalId) {
		const modalEl = document.getElementById(modalId);
		if (!modalEl) {
			return;
		}

		const bsModal = getBootstrapModalInstance(modalEl);
		if (bsModal) {
			bsModal.show();
			return;
		}

		// Fallback sin Bootstrap: muestra el modal manualmente.
		modalEl.classList.add('d-block');
		modalEl.classList.add('show');
		modalEl.setAttribute('aria-modal', 'true');
		modalEl.removeAttribute('aria-hidden');
		document.body.classList.add('modal-open');
	}

	function hideModalById(modalId) {
		const modalEl = document.getElementById(modalId);
		if (!modalEl) {
			return;
		}

		const bsModal = getBootstrapModalInstance(modalEl);
		if (bsModal) {
			bsModal.hide();
			return;
		}

		modalEl.classList.remove('show');
		modalEl.classList.remove('d-block');
		modalEl.setAttribute('aria-hidden', 'true');
		modalEl.removeAttribute('aria-modal');
		document.body.classList.remove('modal-open');
	}

	function configurarBotonesModalFallback() {
		document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(btn) {
			btn.addEventListener('click', function() {
				const modal = btn.closest('.modal');
				if (modal) {
					hideModalById(modal.id);
				}
			});
		});
	}

	function volver() {
		const url = 'index.php?module=adquisiciones&action=requerimientos';
		if (typeof window.cargarVistaAdquisiciones === 'function') {
			window.cargarVistaAdquisiciones(url);
			return;
		}
		window.location.href = url;
	}

	function renderEstadoRequerimiento() {
		const badgeContenedor = document.getElementById('estado-requerimiento-badge');
		const accionesContenedor = document.getElementById('estado-requerimiento-acciones');

		if (badgeContenedor) {
			badgeContenedor.innerHTML = estadoActualRequerimiento === 1 ?
				'<span class="badge bg-success-lt">Completo</span>' :
				'<span class="badge bg-warning-lt text-dark">Pendiente</span>';
		}

		if (accionesContenedor) {
			accionesContenedor.innerHTML = estadoActualRequerimiento === 1 ?
				'<button class="btn btn-warning" onclick="marcarComoPendiente()">Marcar como Pendiente</button>' :
				'<button class="btn btn-success" onclick="marcarComoCompleto()">Marcar como Completo</button>';
		}
	}

	function construirFilaDetalle(id, valores) {
		return [
			'<tr',
				' data-id="' + id + '"',
				' data-id-catalogo-tecnologico="' + escapeHtml(valores.idCatalogoTecnologico) + '"',
				' data-codigo-tecnologia="' + escapeHtml(valores.codigoTecnologia || '') + '"',
				' data-codigo-siga="' + escapeHtml(valores.codigoSiga || '') + '"',
				' data-clasificador="' + escapeHtml(valores.clasificador || '') + '"',
				' data-descripcion-detallada="' + escapeHtml(valores.descripcionDetallada || '') + '"',
				' data-cantidad="' + parseInt(valores.cantidad, 10) + '"',
				' data-unidad-medida="' + escapeHtml(valores.unidadMedida) + '">',
				'<td>' + escapeHtml(valores.codigoSiga || '') + '</td>',
				'<td>' + escapeHtml(valores.clasificador || '') + '</td>',
				'<td>' + escapeHtml(valores.descripcionDetallada || '') + '</td>',
				'<td>' + parseInt(valores.cantidad, 10) + '</td>',
				'<td>' + escapeHtml(valores.codigoTecnologia || '') + '</td>',
				'<td class="text-end align-middle">',
				'<div class="btn-group" role="group">',
					'<button type="button" class="btn btn-icon btn-lg" title="Distribuir" onclick="abrirDistribucionDetalle(' + id + ')">',
						'<i class="ti ti-adjustments-horizontal fs-2"></i>',
					'</button>',
					'<button type="button" class="btn btn-icon btn-lg" title="Editar" onclick="editarDetalle(' + id + ')">',
						'<i class="ti ti-edit fs-2"></i>',
					'</button>',
					'<button type="button" class="btn btn-icon btn-lg text-danger" title="Eliminar" onclick="eliminarDetalle(' + id + ')">',
						'<i class="ti ti-trash fs-2"></i>',
					'</button>',
				'</div>',
				'</td>',
			'</tr>'
		].join('');
	}

	function obtenerFilaDetallePorId(id) {
		return document.querySelector('tr[data-id="' + id + '"]');
	}

	function actualizarSubCentrosDisponibles(idCentroCosto) {
		const subCentroSelect = document.getElementById('distribucion-IdSubCentroCosto');
		if (!subCentroSelect) {
			return;
		}

		subCentroSelect.innerHTML = '<option value="">Sin subcentro</option>';
		const centroSeleccionado = parseInt(idCentroCosto, 10);
		subCentrosCostoDistribucion.forEach(function(subcentro) {
			if (centroSeleccionado > 0 && parseInt(subcentro.IdCentroCosto, 10) !== centroSeleccionado) {
				return;
			}

			const option = document.createElement('option');
			option.value = subcentro.Id;
			option.textContent = subcentro.Siglas ? subcentro.Siglas + ' - ' + subcentro.NombreSubCentroCosto : subcentro.NombreSubCentroCosto;
			subCentroSelect.appendChild(option);
		});
	}

	function calcularResumenDistribucion(totalSolicitado, distribuciones) {
		const totalDistribuido = distribuciones.reduce(function(acc, distribucion) {
			return acc + (parseInt(distribucion.Cantidad, 10) || 0);
		}, 0);
		return {
			totalSolicitado: totalSolicitado,
			totalDistribuido: totalDistribuido,
			totalSaldo: Math.max(0, totalSolicitado - totalDistribuido)
		};
	}

	function renderDistribuciones(distribuciones, totalSolicitado) {
		const tabla = document.getElementById('tabla-distribucion-detalle');
		if (!tabla) {
			return;
		}

		tabla.innerHTML = '';
		if (!Array.isArray(distribuciones) || distribuciones.length === 0) {
			tabla.innerHTML = '<tr><td colspan="4" class="text-center text-secondary">No hay distribuciones registradas.</td></tr>';
			setText('distribucion-total-distribuido', '0');
			setText('distribucion-total-saldo', totalSolicitado);
			return;
		}

		distribuciones.forEach(function(distribucion) {
			tabla.insertAdjacentHTML('beforeend',
				'<tr>' +
					'<td>' + escapeHtml(distribucion.SiglasCentroCosto ? distribucion.SiglasCentroCosto + ' - ' + distribucion.NombreCentroCosto : distribucion.NombreCentroCosto) + '</td>' +
					'<td>' + escapeHtml(distribucion.SiglasSubCentroCosto ? distribucion.SiglasSubCentroCosto + ' - ' + distribucion.NombreSubCentroCosto : (distribucion.NombreSubCentroCosto || 'Sin subcentro')) + '</td>' +
					'<td>' + parseInt(distribucion.Cantidad, 10) + '</td>' +
					'<td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDistribucionDetalle(' + parseInt(distribucion.Id, 10) + ')">Eliminar</button></td>' +
				'</tr>'
			);
		});

		const resumen = calcularResumenDistribucion(totalSolicitado, distribuciones);
		setText('distribucion-total-distribuido', resumen.totalDistribuido);
		setText('distribucion-total-saldo', resumen.totalSaldo);
	}

	function cargarDistribucionesDetalle(idDetalle) {
		return fetch('index.php?module=adquisiciones&action=obtenerDistribucionDetalleAjax&idDetalle=' + encodeURIComponent(idDetalle))
			.then(function(response) {
				return response.text().then(function(text) {
					if (!response.ok) {
						console.error('Respuesta no OK al cargar distribuciones:', response.status, text);
						return { success: false, distribuciones: [] };
					}
					try {
						return JSON.parse(text);
					} catch (error) {
						console.error('JSON inválido al cargar distribuciones:', error, text);
						return { success: false, distribuciones: [] };
					}
				});
			})
			.then(function(result) {
				if (result && result.success) {
					return result.distribuciones || [];
				}
				return [];
			})
			.catch(function(error) {
				console.error('Error cargando distribuciones:', error);
				return [];
			});
	}

	function abrirDistribucionDetalle(idDetalle) {
		const fila = obtenerFilaDetallePorId(idDetalle);
		if (!fila) {
			return;
		}

		const codigoSiga = fila.dataset.codigoSiga || '';
		const descripcionDetallada = fila.dataset.descripcionDetallada || '';
		const cantidadSolicitada = parseInt(fila.dataset.cantidad, 10) || 0;

		setValue('distribucion-Id', '');
		setValue('distribucion-IdDetalleRequerimiento', idDetalle);
		setText('distribucion-item-info', 'Ítem: ' + codigoSiga + ' - ' + descripcionDetallada);
		setText('distribucion-total-solicitado', cantidadSolicitada);
		setValue('distribucion-Cantidad', cantidadSolicitada > 0 ? cantidadSolicitada : 1);

		const centroSelect = document.getElementById('distribucion-IdCentroCosto');
		if (centroSelect) {
			centroSelect.value = '';
		}
		actualizarSubCentrosDisponibles('');

		cargarDistribucionesDetalle(idDetalle)
			.then(function(distribuciones) {
				renderDistribuciones(distribuciones, cantidadSolicitada);
			})
			.catch(function(error) {
				console.error('Error cargando distribuciones:', error);
				renderDistribuciones([], cantidadSolicitada);
			})
			.finally(function() {
				hideModalById('modal-distribucion-detalle');
				showModalById('modal-distribucion-detalle');
			});
	}

	function guardarDistribucionDetalle(event) {
		event.preventDefault();

		const form = document.getElementById('form-distribucion-detalle');
		if (!form) {
			return;
		}

		const idDetalleRequerimiento = parseInt(form.querySelector('#distribucion-IdDetalleRequerimiento').value, 10) || 0;
		const centroCosto = parseInt(form.querySelector('#distribucion-IdCentroCosto').value, 10) || 0;
		const subCentroCosto = form.querySelector('#distribucion-IdSubCentroCosto').value || '';
		const cantidad = parseInt(form.querySelector('#distribucion-Cantidad').value, 10) || 0;
		const totalSolicitado = parseInt(document.getElementById('distribucion-total-solicitado').textContent, 10) || 0;

		if (idDetalleRequerimiento <= 0 || centroCosto <= 0 || cantidad <= 0) {
			window.adqNotifySafe('danger', 'Datos incompletos', 'Complete todos los campos de distribución.');
			return;
		}

		cargarDistribucionesDetalle(idDetalleRequerimiento).then(function(distribuciones) {
			const totalDistribuido = distribuciones.reduce(function(acc, distribucion) {
				return acc + (parseInt(distribucion.Cantidad, 10) || 0);
			}, 0);

			if (cantidad > totalSolicitado - totalDistribuido) {
				window.adqNotifySafe('warning', 'Cantidad no válida', 'La cantidad supera el saldo restante.');
				return;
			}

			const payload = {
				IdDetalleRequerimiento: idDetalleRequerimiento,
				IdCentroCosto: centroCosto,
				IdSubCentroCosto: subCentroCosto,
				Cantidad: cantidad
			};

			const existeDuplicado = distribuciones.some(function(distribucion) {
				const mismoCentro = parseInt(distribucion.IdCentroCosto, 10) === centroCosto;
				const mismoSubcentro = (distribucion.IdSubCentroCosto === null && subCentroCosto === '') || parseInt(distribucion.IdSubCentroCosto || 0, 10) === parseInt(subCentroCosto || 0, 10);
				return mismoCentro && mismoSubcentro;
			});

			if (existeDuplicado) {
				window.adqNotifySafe('warning', 'Duplicado detectado', 'Ya existe una distribución con el mismo centro y subcentro.');
				return;
			}

			postData('guardarDistribucionDetalleAjax', payload)
				.then(function(response) {
					if (response.success) {
						window.adqNotifySafe('success', 'Distribución guardada', response.message || 'Distribución guardada correctamente.');
						return cargarDistribucionesDetalle(idDetalleRequerimiento);
					}
					throw new Error(response.message || 'No se pudo guardar la distribución.');
				})
				.then(function(distribuciones) {
					renderDistribuciones(distribuciones, totalSolicitado);
					setValue('distribucion-Cantidad', totalSolicitado - distribuciones.reduce(function(acc, distribucion) {
						return acc + (parseInt(distribucion.Cantidad, 10) || 0);
					}, 0) || 0);
				})
				.catch(function() {
					window.adqNotifySafe('danger', 'Error de solicitud', 'Ocurrió un error al guardar la distribución.');
				});
		});
	}

	function eliminarDistribucionDetalle(id) {
		postData('eliminarDistribucionDetalleAjax', { id: id })
			.then(function(response) {
				if (response.success) {
					const idDetalleReq = parseInt(document.getElementById('distribucion-IdDetalleRequerimiento').value, 10) || 0;
					const totalSolicitado = parseInt(document.getElementById('distribucion-total-solicitado').textContent, 10) || 0;
					return cargarDistribucionesDetalle(idDetalleReq).then(function(distribuciones) {
						renderDistribuciones(distribuciones, totalSolicitado);
					});
				}
				window.adqNotifySafe('danger', 'No se pudo eliminar', response.message || 'No se pudo eliminar la distribución.');
			})
			.catch(function() {
				window.adqNotifySafe('danger', 'Error de solicitud', 'Ocurrió un error al eliminar la distribución.');
			});
	}

	function upsertFilaDetalle(id, valores, esEdicion) {
		const tablaBody = document.getElementById('tabla-detalles');
		if (!tablaBody) {
			return;
		}

		const filaVacia = tablaBody.querySelector('tr td[colspan="6"]');
		const filaVaciaCompat = tablaBody.querySelector('tr td[colspan="7"]');
		if (filaVacia) {
			tablaBody.innerHTML = '';
		}
		if (filaVaciaCompat) {
			tablaBody.innerHTML = '';
		}

		const htmlFila = construirFilaDetalle(id, valores);
		const filaActual = tablaBody.querySelector('tr[data-id="' + id + '"]');

		if (filaActual && esEdicion) {
			filaActual.outerHTML = htmlFila;
			return;
		}

		tablaBody.insertAdjacentHTML('beforeend', htmlFila);
	}

	function nuevoDetalle() {
		modoEdicion = false;
		const form = document.getElementById('form-detalle');
		if (form) {
			form.reset();
		}
		setValue('detalle-Id', '');
		setValue('detalle-IdRequerimiento', idRequerimiento);
		setValue('detalle-UnidadMedida', 'UND');
		setValue('detalle-IdCatalogoTecnologico', '');
		setValue('detalle-Clasificador', '');
		setText('modal-detalle-title', 'Agregar Ítem - ' + descripcionPedidoConMeta());
		// Si Bootstrap no se dispara por data-bs-*, este fallback lo abre igual.
		showModalById('modal-detalle');
	}

	function obtenerCodigoTecnologiaSeleccionada() {
		const select = document.getElementById('detalle-IdCatalogoTecnologico');
		if (!select || !select.options) {
			return '';
		}

		const selectedIndex = select.selectedIndex;
		if (selectedIndex < 0) {
			return '';
		}

		const option = select.options[selectedIndex];
		return option && option.dataset ? (option.dataset.codigo || '') : '';
	}

	function editarDetalle(id) {
		modoEdicion = true;

		const fila = document.querySelector('tr[data-id="' + id + '"]');
		if (!fila) {
			return;
		}

		const idCatalogoTecnologico = fila.dataset.idCatalogoTecnologico || '';
		const codigoSiga = fila.dataset.codigoSiga || '';
		const clasificador = fila.dataset.clasificador || '';
		const descripcionDetallada = fila.dataset.descripcionDetallada || '';
		const cantidad = fila.dataset.cantidad || '';
		const unidadMedida = fila.dataset.unidadMedida || 'UND';

		setValue('detalle-Id', id);
		setValue('detalle-IdCatalogoTecnologico', idCatalogoTecnologico);
		setValue('detalle-CodigoSiga', codigoSiga);
		setValue('detalle-Clasificador', clasificador);
		setValue('detalle-DescripcionDetallada', descripcionDetallada);
		setValue('detalle-Cantidad', cantidad);
		setValue('detalle-UnidadMedida', unidadMedida);
		setText('modal-detalle-title', 'Editar Ítem - ' + descripcionPedidoConMeta());

		showModalById('modal-detalle');
	}

	function guardarDetalle(event) {
		event.preventDefault();

		const form = document.getElementById('form-detalle');
		if (!form) {
			return;
		}

		const action = modoEdicion ? 'actualizarDetalleAjax' : 'guardarDetalleAjax';
		const formData = new FormData(form);
		const payloadVista = {
			idCatalogoTecnologico: formData.get('IdCatalogoTecnologico') || '',
			codigoTecnologia: obtenerCodigoTecnologiaSeleccionada(),
			codigoSiga: formData.get('CodigoSiga') || '',
			clasificador: formData.get('Clasificador') || '',
			descripcionDetallada: formData.get('DescripcionDetallada') || '',
			cantidad: formData.get('Cantidad') || 0,
			unidadMedida: formData.get('UnidadMedida') || 'UND'
		};

		postForm(action, formData)
			.then(function(response) {
				if (response.success) {
					const idDetalleGuardado = modoEdicion ? (formData.get('Id') || '') : (response.id || '');
					upsertFilaDetalle(idDetalleGuardado, payloadVista, modoEdicion);
					hideModalById('modal-detalle');
					if (!modoEdicion) {
						form.reset();
						setValue('detalle-UnidadMedida', 'UND');
						setValue('detalle-IdCatalogoTecnologico', '');
						setValue('detalle-Clasificador', '');
					}
				} else {
					window.adqNotifySafe('danger', 'No se pudo guardar', response.message || 'No se pudo guardar el item.');
				}
			})
			.catch(function() {
				window.adqNotifySafe('danger', 'Error de solicitud', 'Ocurrio un error al procesar la solicitud.');
			});
	}

	async function eliminarDetalle(id) {
		const confirmado = await window.adqConfirmSafe({
			titulo: 'Confirmar eliminacion',
			mensaje: 'Se eliminara este item del requerimiento.',
			textoAceptar: 'Eliminar',
			textoCancelar: 'Cancelar',
			claseAceptar: 'btn-danger'
		});

		if (!confirmado) {
			return;
		}

		postData('eliminarDetalleAjax', {
				id: id
			})
			.then(function(response) {
				if (response.success) {
					const fila = document.querySelector('tr[data-id="' + id + '"]');
					if (fila) {
						fila.remove();
					}

					const tablaBody = document.getElementById('tabla-detalles');
					if (tablaBody && tablaBody.querySelectorAll('tr[data-id]').length === 0) {
						tablaBody.innerHTML = '<tr><td colspan="7" class="text-center text-secondary">No hay ítems registrados.</td></tr>';
					}
				} else {
					window.adqNotifySafe('danger', 'No se pudo eliminar', response.message || 'No se pudo eliminar el item.');
				}
			})
			.catch(function() {
				window.adqNotifySafe('danger', 'Error de solicitud', 'Ocurrio un error al procesar la solicitud.');
			});
	}

	function marcarComoCompleto() {
		postData('actualizarEstadoAjax', {
				id: idRequerimiento,
				estado: 1
			})
			.then(function(response) {
				if (response.success) {
					estadoActualRequerimiento = 1;
					renderEstadoRequerimiento();
				} else {
					window.adqNotifySafe('danger', 'No se pudo actualizar', response.message || 'No se pudo actualizar el estado.');
				}
			})
			.catch(function() {
				window.adqNotifySafe('danger', 'Error de solicitud', 'Ocurrio un error al procesar la solicitud.');
			});
	}

	function marcarComoPendiente() {
		postData('actualizarEstadoAjax', {
				id: idRequerimiento,
				estado: 0
			})
			.then(function(response) {
				if (response.success) {
					estadoActualRequerimiento = 0;
					renderEstadoRequerimiento();
				} else {
					window.adqNotifySafe('danger', 'No se pudo actualizar', response.message || 'No se pudo actualizar el estado.');
				}
			})
			.catch(function() {
				window.adqNotifySafe('danger', 'Error de solicitud', 'Ocurrio un error al procesar la solicitud.');
			});
	}

	function inicializarVistaDetalleRequerimiento() {
		const btnAgregar = document.getElementById('btn-agregar-item');
		const formDetalle = document.getElementById('form-detalle');
		const formDistribucion = document.getElementById('form-distribucion-detalle');
		const selectCentroDistribucion = document.getElementById('distribucion-IdCentroCosto');

		if (btnAgregar && !btnAgregar.dataset.inicializado) {
			btnAgregar.addEventListener('click', nuevoDetalle);
			btnAgregar.dataset.inicializado = '1';
		}
		if (formDetalle && !formDetalle.dataset.inicializado) {
			formDetalle.addEventListener('submit', guardarDetalle);
			formDetalle.dataset.inicializado = '1';
		}
		if (formDistribucion && !formDistribucion.dataset.inicializado) {
			formDistribucion.addEventListener('submit', guardarDistribucionDetalle);
			formDistribucion.dataset.inicializado = '1';
		}
		if (selectCentroDistribucion && !selectCentroDistribucion.dataset.inicializado) {
			selectCentroDistribucion.addEventListener('change', function() {
				actualizarSubCentrosDisponibles(this.value);
			});
			selectCentroDistribucion.dataset.inicializado = '1';
		}
		configurarBotonesModalFallback();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', inicializarVistaDetalleRequerimiento);
	} else {
		inicializarVistaDetalleRequerimiento();
	}
</script>
