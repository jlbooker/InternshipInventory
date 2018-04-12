<?php
/**
 * This file is part of Internship Inventory.
 *
 * Internship Inventory is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Internship Inventory is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License version 3
 * along with Internship Inventory.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2011-2018 Appalachian State University
 */

namespace Intern;

require_once PHPWS_SOURCE_DIR . 'mod/intern/vendor/autoload.php';

/**
 * InternshipContractPdfView
 *
 * View class for generating a PDF of an internship.
 *
 * @author jbooker
 * @package Intern
 */

class InternshipContractPdfView {

    private $internship;
    private $emergencyContacts;
    private $term;

    private $pdf;

    /**
     * Creates a new InternshipContractPdfView
     *
     * @param Internship $i
     * @param Array<EmergencyContact> $emergencyContacts
     * @param Term $term
     */
    public function __construct(Internship $i, Array $emergencyContacts, Term $term)
    {
        $this->internship = $i;
        $this->emergencyContacts = $emergencyContacts;
        $this->term = $term;

        $this->generatePdf();
    }

    /**
     * Returns the FPDI (FPDF) object which was generated by this view.
     *
     * @return FPDI
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * Does the hard work of generating a PDF.
     */
    private function generatePdf()
    {
        $this->pdf = new \setasign\Fpdi\Fpdi('P', 'mm', 'Letter');
        $a = $this->internship->getAgency();
        $d = $this->internship->getDepartment();
        $f = $this->internship->getFaculty();
        //$m = $this->internship->getUgradMajor();
        //$g = $this->internship->getGradProgram();
        //$subject = $this->internship->getSubject();

        //$pagecount = $this->pdf->setSourceFile(PHPWS_SOURCE_DIR . 'mod/intern/pdf/AppStateInternshipContractNew.pdf');
        $this->pdf->setSourceFile(PHPWS_SOURCE_DIR . 'mod/intern/pdf/AppStateInternshipContractNew.pdf');
        $tplidx = $this->pdf->importPage(1);
        $this->pdf->addPage();
        $this->pdf->useTemplate($tplidx);

        $this->pdf->setFont('Times', null, 10);
        $this->pdf->setAutoPageBreak(true, 0);

        /**************************
         * Internship information *
        */

        /* Department */
        $this->pdf->setXY(138, 40);
        $this->pdf->multiCell(73, 3, $d->getName());

        /* Course title */
        $this->pdf->setXY(138, 52);
        $this->pdf->cell(73, 6, $this->internship->getCourseTitle());

        /* Location center aligned*/
        if($this->internship->isDomestic()){
            $this->pdf->setXY(85, 68);
            $this->pdf->cell(24, 5, 'X', 0, 0, 'C');
        }
        if($this->internship->isInternational()){
            $this->pdf->setXY(168, 68);
            $this->pdf->cell(24, 5, 'X', 0, 0, 'C');
        }

        /**
         * Student information.
         */
        $this->pdf->setXY(40, 84);
        $this->pdf->cell(55, 5, $this->internship->getFullName());

        $this->pdf->setXY(155, 84);
        $this->pdf->cell(42, 5, $this->internship->getBannerId());

        $this->pdf->setXY(41, 94);
        $this->pdf->cell(54, 5, $this->internship->getEmailAddress() . '@appstate.edu');

        $this->pdf->setXY(127, 94);
        $this->pdf->cell(54, 5, $this->internship->getPhoneNumber());

        /* Student Address */
        $this->pdf->setXY(60, 89);
        $this->pdf->cell(54, 5, $this->internship->getStudentAddress());


        /* Payment */
        if($this->internship->isPaid()){
            $this->pdf->setXY(25, 99);
            $this->pdf->cell(10,5, 'X');
        }else {
            $this->pdf->setXY(87, 99);
            $this->pdf->cell(10,5,'X');
        }

        // Stipend
        if($this->internship->hasStipend()) {
            $this->pdf->setXY(56, 99);
            $this->pdf->cell(10,5, 'X');
        }

        /* Hours */
        $this->pdf->setXY(190, 100);
        $this->pdf->cell(12, 5, $this->internship->getCreditHours());

        // Hours per week
        $this->pdf->setXY(147, 100);
        $this->pdf->cell(12, 5, $this->internship->getAvgHoursPerWeek());

        /* Term right aligned*/
        $this->pdf->setXY(1, 103);
        $this->pdf->cell(27, 6, $this->term->getDescription(), 0, 0, 'R');

        /* Dates for begining and end of term center aligned*/
        $this->pdf->setXY(87, 106);
        $this->pdf->cell(30, 5, $this->term->getStartDateFormatted(), 0, 0, 'C');
        $this->pdf->setXY(160, 106);
        $this->pdf->cell(30, 5, $this->term->getEndDateFormatted(), 0, 0, 'C');

        /***
         * Faculty supervisor information.
         */
        if(isset($f)){
            $this->pdf->setXY(28, 119);
            $this->pdf->cell(81, 5, $f->getFullName());

            $this->pdf->setXY(31, 126);
            $this->pdf->cell(81, 5, $f->getStreetAddress1());

            $this->pdf->setXY(16, 133);
            $this->pdf->cell(81, 5, $f->getStreetAddress2());

            $this->pdf->setXY(60, 133);
            $this->pdf->cell(81, 5, $f->getCity());

            $this->pdf->setXY(88, 133);
            $this->pdf->cell(81, 5, $f->getState());

            $this->pdf->setXY(95, 133);
            $this->pdf->cell(81, 5, $f->getZip());

            $this->pdf->setXY(29, 140);
            $this->pdf->cell(77, 5, $f->getPhone());

            $this->pdf->setXY(25, 147);
            $this->pdf->cell(77, 5, $f->getFax());

            $this->pdf->setXY(28, 154);
            $this->pdf->cell(77, 5, $f->getUsername() . '@appstate.edu');
        }

        /***
         * Agency information.
        */
        $this->pdf->setXY(139, 117);
        $this->pdf->cell(71, 5, $a->getName());

        $agency_address = $a->getStreetAddress();

        //TODO: make this smarter so it adds the line break between words
        if(strlen($agency_address) < 49){
            // If it's short enough, just write it
            $this->pdf->setXY(127, 122);
            $this->pdf->cell(77, 5, $agency_address);
        }else{
            // Too long, need to use two lines
            $agencyLine1 = substr($agency_address, 0, 49); // get first 50 chars
            $agencyLine2 = substr($agency_address, 49); // get the rest, hope it fits

            $this->pdf->setXY(127, 122);
            $this->pdf->cell(77, 5, $agencyLine1);
            $this->pdf->setXY(113, 127);
            $this->pdf->cell(77, 5, $agencyLine2);
        }

        /**
         * Agency supervisor info.
         */
        $this->pdf->setXY(113, 138);
        $super = "";
        $superName = $a->getSupervisorFullName();
        if(isset($superName) && !empty($superName) && $superName != ''){
            //test('ohh hai',1);
            $super .= $a->getSupervisorFullName();
        }

        $supervisorTitle = $a->getSupervisorTitle();

        if(isset($a->supervisor_title) && !empty($a->supervisor_title)){
            $super .= ', ' . $supervisorTitle;
        }
        $this->pdf->cell(75, 5, $super);

        $super_address = $a->getSuperAddress();
        //TODO: make this smarter so it adds the line break between words
        if(strlen($super_address) < 54){
            // If it's short enough, just write it
            $this->pdf->setXY(113, 143);
            $this->pdf->cell(78, 5, $super_address);
        }else{
            // Too long, need to use two lines
            $superLine1 = substr($super_address, 0, 54); // get first 55 chars
            $superLine2 = substr($super_address, 54); // get the rest, hope it fits

            $this->pdf->setXY(113, 143);
            $this->pdf->cell(78, 5, $superLine1);
            $this->pdf->setXY(113, 148);
            $this->pdf->cell(78, 5, $superLine2);
        }

        $this->pdf->setXY(125, 159);
        $this->pdf->cell(72, 5, $a->getSupervisorEmail());

        $this->pdf->setXY(125, 154);
        $this->pdf->cell(33, 5, $a->getSupervisorPhoneNumber());

        $this->pdf->setXY(166, 154);
        $this->pdf->cell(40, 5, $a->getSupervisorFaxNumber());

        /* Internship Location */
        $internshipAddress = trim($this->internship->getStreetAddress());
        $agencyAddress = trim($a->getStreetAddress());

        if($internshipAddress != '' && $agencyAddress != '' && $internshipAddress != $agencyAddress) {
            $this->pdf->setXY(112, 169);
            $this->pdf->cell(52, 5, $this->internship->getLocationAddress());
        }


        /**********
         * Page 2 *
        **********/
        $tplidx = $this->pdf->importPage(2);
        $this->pdf->addPage();
        $this->pdf->useTemplate($tplidx);

        /* Emergency Contact Info */
        if(sizeof($this->emergencyContacts) > 0){
            $firstContact = $this->emergencyContacts[0];

            $this->pdf->setXY(60, 274);
            $this->pdf->cell(52, 0, $firstContact->getName());

            $this->pdf->setXY(134, 274);
            $this->pdf->cell(52, 0, $firstContact->getRelation());

            $this->pdf->setXY(175, 274);
            $this->pdf->cell(52, 0, $firstContact->getPhone());
        }
    }
}
