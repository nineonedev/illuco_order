import Controller from "../core/Controller";
import Modal from '../shared/Modal';
import Loader from '../shared/Loader';
import Ajax from "../core/Ajax";

import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';

export default class OrderController extends Controller {
    loader;
    modal;
    form;
    cancelBtn;

    // print(){
    //     const element = document.getElementById('print-area');

    //     if (!element) {
    //         console.error('print-area not found!');
    //         return;
    //     }

    //     const printBtn = document.getElementById('print-btn');

    //     if(!printBtn) {
    //         console.error('print-btn not found!');
    //         return;
    //     }

    //     printBtn.addEventListener('click', () => {
    //         html2pdf(element, {
    //             margin:       0,
    //             filename:     element.dataset.pdfName || 'order-document',
    //             image:        { type: 'jpeg', quality: 0.98 },
    //             html2canvas:  { scale: 2, useCORS: true, dpi: 300},
    //             jsPDF:        { unit: 'mm', format: 'a4', orientation: element.dataset.page || 'portrait' }
    //         });
    //     });
        
    // }

    print() {
        const printBtn = document.getElementById('print-btn');
        const element = document.getElementById('print-area');

        if (!printBtn || !element) {
            console.error('print-btn or print-area not found!');
            return;
        }

        printBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            this.loader = this.loader || Loader.make('portal').render();
            this.loader.show();

            try {
                const filename = element.dataset.pdfName || 'order-document.pdf';
                const orientation = element.dataset.page || 'portrait'; // 'portrait' or 'landscape'
                
                await this.exportToPdf(element, filename, orientation);
            } catch (err) {
                console.error('PDF 생성 실패:', err);
            } finally {
                this.loader.hide();
            }
        });
    }

    async exportToPdf(element, filename = 'document.pdf', orientation = 'portrait') {
        const canvas = await html2canvas(element, {
            scale: 2,
            useCORS: true,
            logging: false
        });

        const imgData = canvas.toDataURL('image/jpeg', 1.0);
        const pdf = new jsPDF(orientation === 'landscape' ? 'l' : 'p', 'mm', 'a4');

        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();

        const imgWidth = pageWidth;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        let heightLeft = imgHeight;
        let position = 0;

        // 첫 페이지
        pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // 나머지 페이지
        while (heightLeft > 0) {
            position -= pageHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        // ✅ 페이지 번호 추가
        const pageCount = pdf.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            pdf.setPage(i);
            pdf.setFontSize(10);
            pdf.setTextColor(100);

            // 페이지 오른쪽 하단에 "i / total" 표시
            pdf.text(
                `${i} / ${pageCount}`,
                pageWidth - 20,   // X 좌표 (오른쪽 여백 20mm)
                pageHeight - 10   // Y 좌표 (아래 여백 10mm)
            );
        }

        pdf.save(filename);
    }


    edit() {
        this._logger.info("index");
        this.modal = Modal.make('portal').render();
        this.loader = Loader.make('portal').render();
        
        const form = document.getElementById('frm'); 
        this.form = form;

        this.cancelBtn = document.querySelector('[data-action="cancel"]');

        form.addEventListener('submit', this._update.bind(this)); 
    }

    async _update(e) {
        e.preventDefault();
        
        const button = e.submitter; 
        const form = e.target; 
        this._logger.info(button, form); 
        
        try {
            button.disabled = true; 
            this.loader.show();
            const fd = new FormData(form);
            const result = await new Ajax(false).put(form.action, fd);
            this._logger.success(result);

            if (result.success) {
                if (this.cancelBtn) {
                    this.cancelBtn.click(); 
                } else {
                    location.reload(); 
                }
            }   
            
        } finally {
            this.loader.hide();
            button.disabled = false; 
        }
    }
}
