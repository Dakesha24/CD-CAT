<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-primary"><?= esc($bankUjian['nama_ujian']) ?></h2>
            <p class="text-muted mb-2">
                <i class="fas fa-tag me-2"></i><?= esc($bankUjian['nama_jenis']) ?> - 
                <?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?>
            </p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('guru/dashboard') ?>" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('guru/bank-soal') ?>" class="text-decoration-none">Bank Soal</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('guru/bank-soal/kategori/' . urlencode($kategori)) ?>" class="text-decoration-none">
                            <?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('guru/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $bankUjian['jenis_ujian_id']) ?>" class="text-decoration-none">
                            <?= esc($bankUjian['nama_jenis']) ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= esc($bankUjian['nama_ujian']) ?>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <a href="<?= base_url('guru/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $bankUjian['jenis_ujian_id']) ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
                <?php if ($canEdit): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
                        <i class="fas fa-plus me-2"></i>Tambah Soal
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Info Bank Ujian -->
    <?php if (!empty($bankUjian['deskripsi'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i><?= esc($bankUjian['deskripsi']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Info Box untuk Panduan Rumus -->
    <div class="alert alert-light alert-dismissible fade show border" role="alert">
        <h6 class="alert-heading"><i class="fas fa-lightbulb me-2 text-warning"></i>Tips Menulis Soal</h6>
        <p class="mb-2">Untuk menulis rumus matematika, gunakan format LaTeX dengan tanda dollar ($):</p>
        <ul class="mb-2 small">
            <li><strong>Inline:</strong> <code>$x^2 + y^2 = z^2$</code></li>
            <li><strong>Pecahan:</strong> <code>$\frac{a}{b}$</code></li>
            <li><strong>Akar:</strong> <code>$\sqrt{x}$</code></li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <?php if (empty($soalList)): ?>
        <!-- Jika belum ada soal -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm text-center">
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="fas fa-question-circle fa-3x text-muted"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-muted mb-3">Belum Ada Soal</h5>
                        <p class="text-muted mb-4">
                            Belum ada soal yang dibuat untuk bank ujian 
                            <strong><?= esc($bankUjian['nama_ujian']) ?></strong>
                        </p>
                        <?php if ($canEdit): ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
                                <i class="fas fa-plus me-2"></i>Tambah Soal Pertama
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Daftar Soal -->
        <div class="card shadow-sm">
            <div class="card-header bg-light border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-list me-2 text-primary"></i>Daftar Soal
                    </h5>
                    <span class="badge bg-primary"><?= count($soalList) ?> soal</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="px-3">No</th>
                                <th width="35%">Pertanyaan</th>
                                <th width="8%" class="text-center">Jawaban</th>
                                <th width="10%" class="text-center">Kesulitan</th>
                                <th width="8%" class="text-center">Foto</th>
                                <th width="12%" class="text-center">Dibuat</th>
                                <?php if ($canEdit): ?>
                                    <th width="22%" class="text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($soalList as $index => $soal): ?>
                            <tr>
                                <td class="px-3 fw-semibold"><?= $index + 1 ?></td>
                                <td>
                                    <div class="text-truncate math-content" style="max-width: 300px;" title="<?= esc($soal['pertanyaan']) ?>">
                                        <?= esc($soal['pertanyaan']) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6"><?= esc($soal['jawaban_benar']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $soal['tingkat_kesulitan'] <= 0.3 ? 'success' : ($soal['tingkat_kesulitan'] <= 0.7 ? 'warning' : 'danger') ?>">
                                        <?= number_format($soal['tingkat_kesulitan'], 2) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($soal['foto'])): ?>
                                        <i class="fas fa-image text-success" title="Ada foto"></i>
                                    <?php else: ?>
                                        <i class="fas fa-minus text-muted" title="Tidak ada foto"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($soal['created_at'])) ?>
                                    </small>
                                </td>
                                <?php if ($canEdit): ?>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="fw-bold btn btn-info" data-bs-toggle="modal" data-bs-target="#detailModal<?= $soal['soal_id'] ?>" title="Lihat Detail">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </button>
                                        <button type="button" class="fw-bold btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $soal['soal_id'] ?>" title="Edit">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                        <button type="button" class="fw-bold btn btn-danger" onclick="hapusSoal(<?= $soal['soal_id'] ?>)" title="Hapus">
                                            <i class="fas fa-trash me-1"></i>Hapus
                                        </button>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Soal -->
<?php if ($canEdit): ?>
<div class="modal fade" id="modalTambahSoal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Tambah Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('guru/bank-soal/tambah-soal') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="bank_ujian_id" value="<?= $bankUjian['bank_ujian_id'] ?>">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Pertanyaan</label>
                            <textarea name="pertanyaan" class="form-control math-preview" rows="3" required placeholder="Masukkan pertanyaan. Gunakan $...$ untuk rumus matematika."></textarea>
                            <div class="mt-2 p-2 border rounded bg-light">
                                <small class="text-muted">Preview:</small>
                                <div id="preview-pertanyaan" class="math-content mt-1"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Foto Soal (Opsional)</label>
                            <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">Format: JPG, JPEG, PNG (maks. 2MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pilihan A</label>
                            <textarea name="pilihan_a" class="form-control math-preview" rows="2" required data-preview="a"></textarea>
                            <div class="mt-1 p-1 border rounded bg-light">
                                <div id="preview-a" class="math-content small"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan B</label>
                            <textarea name="pilihan_b" class="form-control math-preview" rows="2" required data-preview="b"></textarea>
                            <div class="mt-1 p-1 border rounded bg-light">
                                <div id="preview-b" class="math-content small"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan C</label>
                            <textarea name="pilihan_c" class="form-control math-preview" rows="2" required data-preview="c"></textarea>
                            <div class="mt-1 p-1 border rounded bg-light">
                                <div id="preview-c" class="math-content small"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan D</label>
                            <textarea name="pilihan_d" class="form-control math-preview" rows="2" required data-preview="d"></textarea>
                            <div class="mt-1 p-1 border rounded bg-light">
                                <div id="preview-d" class="math-content small"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan E (Opsional)</label>
                            <textarea name="pilihan_e" class="form-control math-preview" rows="2" data-preview="e"></textarea>
                            <div class="mt-1 p-1 border rounded bg-light">
                                <div id="preview-e" class="math-content small"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jawaban Benar</label>
                            <select name="jawaban_benar" class="form-select" required>
                                <option value="">Pilih Jawaban</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tingkat Kesulitan</label>
                            <input type="number" name="tingkat_kesulitan" class="form-control" step="0.01" min="0" max="1" value="0.5" required>
                            <small class="text-muted">Rentang 0.0 (mudah) - 1.0 (sulit)</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Pembahasan (Opsional)</label>
                            <textarea name="pembahasan" class="form-control math-preview" rows="3" data-preview="pembahasan"></textarea>
                            <div class="mt-2 p-2 border rounded bg-light">
                                <small class="text-muted">Preview:</small>
                                <div id="preview-pembahasan" class="math-content mt-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal Detail Soal -->
<?php foreach ($soalList as $soal): ?>
<div class="modal fade" id="detailModal<?= $soal['soal_id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Detail Soal #<?= $soal['soal_id'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Jawaban Benar:</strong> <span class="badge bg-success ms-2"><?= $soal['jawaban_benar'] ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Tingkat Kesulitan:</strong> <span class="badge bg-info ms-2"><?= $soal['tingkat_kesulitan'] ?></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Pertanyaan:</strong>
                    <div class="p-3 bg-light rounded math-content"><?= $soal['pertanyaan'] ?></div>
                </div>

                <?php if (!empty($soal['foto'])): ?>
                    <div class="text-center mb-3">
                        <img src="<?= base_url('uploads/soal/' . $soal['foto']) ?>" alt="Foto Soal" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <strong>Pilihan Jawaban:</strong>
                    <div class="mt-2">
                        <div class="d-flex mb-2 math-content"><span class="badge bg-primary me-2">A</span> <?= $soal['pilihan_a'] ?></div>
                        <div class="d-flex mb-2 math-content"><span class="badge bg-primary me-2">B</span> <?= $soal['pilihan_b'] ?></div>
                        <div class="d-flex mb-2 math-content"><span class="badge bg-primary me-2">C</span> <?= $soal['pilihan_c'] ?></div>
                        <div class="d-flex mb-2 math-content"><span class="badge bg-primary me-2">D</span> <?= $soal['pilihan_d'] ?></div>
                        <?php if (!empty($soal['pilihan_e'])): ?>
                            <div class="d-flex mb-2 math-content"><span class="badge bg-primary me-2">E</span> <?= $soal['pilihan_e'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($soal['pembahasan'])): ?>
                    <div class="card bg-light">
                        <div class="card-header"><strong>Pembahasan</strong></div>
                        <div class="card-body math-content"><?= nl2br($soal['pembahasan']) ?></div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Modal Edit Soal -->
<?php foreach ($soalList as $soal): ?>
<div class="modal fade" id="editModal<?= $soal['soal_id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Soal #<?= $soal['soal_id'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('guru/bank-soal/edit-soal/' . $soal['soal_id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Pertanyaan</label>
                            <textarea name="pertanyaan" class="form-control" rows="3" required><?= $soal['pertanyaan'] ?></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Foto Soal</label>
                            <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                            <?php if (!empty($soal['foto'])): ?>
                                <div class="mt-2">
                                    <img src="<?= base_url('uploads/soal/' . $soal['foto']) ?>" alt="Foto Soal" class="img-thumbnail" style="max-height: 100px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="hapus_foto" value="1">
                                        <label class="form-check-label">Hapus foto yang ada</label>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pilihan A</label>
                            <textarea name="pilihan_a" class="form-control" rows="2" required><?= $soal['pilihan_a'] ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan B</label>
                            <textarea name="pilihan_b" class="form-control" rows="2" required><?= $soal['pilihan_b'] ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan C</label>
                            <textarea name="pilihan_c" class="form-control" rows="2" required><?= $soal['pilihan_c'] ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan D</label>
                            <textarea name="pilihan_d" class="form-control" rows="2" required><?= $soal['pilihan_d'] ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan E (Opsional)</label>
                            <textarea name="pilihan_e" class="form-control" rows="2"><?= $soal['pilihan_e'] ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jawaban Benar</label>
                            <select name="jawaban_benar" class="form-select" required>
                                <option value="A" <?= $soal['jawaban_benar'] == 'A' ? 'selected' : '' ?>>A</option>
                                <option value="B" <?= $soal['jawaban_benar'] == 'B' ? 'selected' : '' ?>>B</option>
                                <option value="C" <?= $soal['jawaban_benar'] == 'C' ? 'selected' : '' ?>>C</option>
                                <option value="D" <?= $soal['jawaban_benar'] == 'D' ? 'selected' : '' ?>>D</option>
                                <option value="E" <?= $soal['jawaban_benar'] == 'E' ? 'selected' : '' ?>>E</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tingkat Kesulitan</label>
                            <input type="number" name="tingkat_kesulitan" class="form-control" step="0.01" min="0" max="1" value="<?= $soal['tingkat_kesulitan'] ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Pembahasan (Opsional)</label>
                            <textarea name="pembahasan" class="form-control" rows="3"><?= $soal['pembahasan'] ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Load MathJax dan Script -->
<script>
window.MathJax = {
    tex: {
        inlineMath: [['$', '$']],
        displayMath: [['$$', '$$']],
        processEscapes: true,
        processEnvironments: true
    },
    options: {
        skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre'],
        ignoreHtmlClass: 'tex2jax_ignore',
        processHtmlClass: 'math-content'
    },
    startup: {
        ready: function () {
            MathJax.startup.defaultReady();
            setupMathPreview();
        }
    }
};

function setupMathPreview() {
    function updatePreview(textarea, previewId) {
        const content = textarea.value;
        const previewElement = document.getElementById(previewId);
        if (previewElement) {
            previewElement.innerHTML = content;
            MathJax.typesetPromise([previewElement]).catch((err) => console.log(err));
        }
    }
    
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('math-preview')) {
            const textarea = e.target;
            const previewId = textarea.getAttribute('data-preview') || 
                            (textarea.name === 'pertanyaan' ? 'pertanyaan' : 
                             textarea.name === 'pembahasan' ? 'pembahasan' : '');
            
            if (previewId) {
                updatePreview(textarea, 'preview-' + previewId);
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle modal cleanup
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    });
});

function hapusSoal(soalId) {
    if (confirm('Apakah Anda yakin ingin menghapus soal ini? Tindakan ini tidak dapat dibatalkan.')) {
        window.location.href = '<?= base_url('guru/bank-soal/hapus-soal/') ?>' + soalId;
    }
}
</script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<style>
.math-content {
    min-height: 20px;
}

.math-preview {
    font-family: 'Courier New', monospace;
}

.card {
    border: none;
    transition: all 0.3s ease;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.btn-group-sm .btn {
    --bs-btn-padding-y: 0.25rem;
    --bs-btn-padding-x: 0.75rem;
    --bs-btn-font-size: 0.875rem;
}

.breadcrumb {
    background: none;
    padding: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
    font-weight: bold;
}

.breadcrumb-item.active {
    color: #6c757d;
    font-weight: 500;
}

.breadcrumb-item a {
    color: #0d6efd;
    font-weight: 500;
}

.breadcrumb-item a:hover {
    color: #0b5ed7;
}

.modal-content {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.alert-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-left: 4px solid #ffc107;
}
</style>

<?= $this->endSection() ?>