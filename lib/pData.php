<?php
/**
 * pData - Simplifying data population for pChart
 * @copyright 2008 Jean-Damien POGOLOTTI
 * @version 1.13 last updated on 08/17/08
 *
 * http://pchart.sourceforge.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 1,2,3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Class initialisation :
 *  pData()
 * Data populating methods :
 *  ImportFromCSV($FileName,$Delimiter=",",$DataColumns=-1,$HasHeader=FALSE,$DataName=-1)
 *  AddPoint($Value,$Serie="Serie1",$Description="")
 * Series manipulation methods :
 *  AddSerie($SerieName="Serie1")
 *  AddAllSeries()
 *  RemoveSerie($SerieName="Serie1")
 *  SetAbsciseLabelSerie($SerieName = "Name")
 *  SetSerieName($Name,$SerieName="Serie1")
 *  SetSerieSymbol($Name,$Symbol)
 *  SetXAxisName($Name="X Axis")
 *  SetYAxisName($Name="Y Axis")
 *  SetXAxisFormat($Format="number")
 *  SetYAxisFormat($Format="number")
 *  SetXAxisUnit($Unit="")
 *  SetYAxisUnit($Unit="")
 *  removeSerieName($SerieName)
 *  removeAllSeries()
 * Data retrieval methods :
 *  GetData()
 *  GetDataDescription()
 */
class pData {
	protected $Data;
	protected $DataDescription;
	
	public function __construct() {
		$this->Data = "";
		$this->DataDescription = "";
		$this->DataDescription ["Position"] = "Name";
		$this->DataDescription ["Format"] ["X"] = "number";
		$this->DataDescription ["Format"] ["Y"] = "number";
		$this->DataDescription ["Unit"] ["X"] = NULL;
		$this->DataDescription ["Unit"] ["Y"] = NULL;
	}
	
	public function ImportFromCSV($FileName, $Delimiter = ",", $DataColumns = -1, $HasHeader = FALSE, $DataName = -1) {
		$handle = @fopen ( $FileName, "r" );
		if ($handle) {
			$HeaderParsed = FALSE;
			while ( ! feof ( $handle ) ) {
				$buffer = fgets ( $handle, 4096 );
				$buffer = str_replace ( chr ( 10 ), "", $buffer );
				$buffer = str_replace ( chr ( 13 ), "", $buffer );
				$Values = split ( $Delimiter, $buffer );
				
				if ($buffer != "") {
					if ($HasHeader == TRUE && $HeaderParsed == FALSE) {
						if ($DataColumns == - 1) {
							$ID = 1;
							foreach ( $Values as $key => $Value ) {
								$this->SetSerieName ( $Value, "Serie" . $ID );
								$ID ++;
							}
						} else {
							$SerieName = "";
							
							foreach ( $DataColumns as $key => $Value )
								$this->SetSerieName ( $Values [$Value], "Serie" . $Value );
						}
						$HeaderParsed = TRUE;
					} else {
						if ($DataColumns == - 1) {
							$ID = 1;
							foreach ( $Values as $key => $Value ) {
								$this->AddPoint ( intval ( $Value ), "Serie" . $ID );
								$ID ++;
							}
						} else {
							$SerieName = "";
							if ($DataName != - 1)
								$SerieName = $Values [$DataName];
							
							foreach ( $DataColumns as $key => $Value )
								$this->AddPoint ( $Values [$Value], "Serie" . $Value, $SerieName );
						}
					}
				}
			}
			fclose ( $handle );
		}
	}
	
	public function AddPoint($Value, $Serie = "Serie1", $Description = "") {
		if (is_array ( $Value ) && count ( $Value ) == 1)
			$Value = $Value [0];
		
		$ID = 0;
		for($i = 0; $i <= count ( $this->Data ); $i ++) {
			if (isset ( $this->Data [$i] [$Serie] )) {
				$ID = $i + 1;
			}
		}
		
		if (count ( $Value ) == 1) {
			$this->Data [$ID] [$Serie] = $Value;
			if ($Description != "")
				$this->Data [$ID] ["Name"] = $Description;
			elseif (! isset ( $this->Data [$ID] ["Name"] ))
				$this->Data [$ID] ["Name"] = $ID;
		} else {
			foreach ( $Value as $key => $Val ) {
				$this->Data [$ID] [$Serie] = $Val;
				if (! isset ( $this->Data [$ID] ["Name"] ))
					$this->Data [$ID] ["Name"] = $ID;
				$ID ++;
			}
		}
	}
	
	/**
	 * Alias for AddSerie
	 * @param string $SerieName
	 * @see AddSerie
	 */
	public function AddSeries($SerieName = "Serie1") {
		$this->AddSerie($SerieName);
	}
	
	public function AddSerie($SerieName = "Serie1") {
		if (! isset ( $this->DataDescription ["Values"] )) {
			$this->DataDescription ["Values"] [] = $SerieName;
		} else {
			$Found = FALSE;
			foreach ( $this->DataDescription ["Values"] as $key => $Value )
				if ($Value == $SerieName) {
					$Found = TRUE;
				}
			
			if (! $Found)
				$this->DataDescription ["Values"] [] = $SerieName;
		}
	}
	
	public function AddAllSeries() {
		unset ( $this->DataDescription ["Values"] );
		
		if (isset ( $this->Data [0] )) {
			foreach ( $this->Data [0] as $Key => $Value ) {
				if ($Key != "Name")
					$this->DataDescription ["Values"] [] = $Key;
			}
		}
	}
	
	/**
	 * Alias for RemoveSerie
	 * @param string $SerieName
	 * @see RemoveSerie()
	 */
	public function RemoveSeries($SerieName = "Serie1") {
		$this->RemoveSerie($SerieName);
	}
	
	public function RemoveSerie($SerieName = "Serie1") {
		if (! isset ( $this->DataDescription ["Values"] ))
			return (0);
		
		$Found = FALSE;
		foreach ( $this->DataDescription ["Values"] as $key => $Value ) {
			if ($Value == $SerieName)
				unset ( $this->DataDescription ["Values"] [$key] );
		}
	}
	
	/**
	 * Alilas for SetAbsciseLabelSerie
	 * @param string $SerieName
	 * @see SetAbsciseLabelSerie
	 */
	public function SetAbsciseLabelSeries($SerieName = "Name") {
		$this->SetAbsciseLabelSerie($SerieName);
	}
		
	public function SetAbsciseLabelSerie($SerieName = "Name") {
		$this->DataDescription ["Position"] = $SerieName;
	}
	
	/**
	 * Alias for SetSerieName
	 * @param string $Name
	 * @param string $SerieName
	 * @see SetSerieName()
	 */
	public function SetSeriesName($Name, $SerieName = "Serie1") {
		$this->SetSerieName($Name, $SerieName);
	}
	
	public function SetSerieName($Name, $SerieName = "Serie1") {
		$this->DataDescription ["Description"] [$SerieName] = $Name;
	}
	
	public function SetXAxisName($Name = "X Axis") {
		$this->DataDescription ["Axis"] ["X"] = $Name;
	}
	
	public function SetYAxisName($Name = "Y Axis") {
		$this->DataDescription ["Axis"] ["Y"] = $Name;
	}
	
	public function SetXAxisFormat($Format = "number") {
		$this->DataDescription ["Format"] ["X"] = $Format;
	}
	
	public function SetYAxisFormat($Format = "number") {
		$this->DataDescription ["Format"] ["Y"] = $Format;
	}
	
	public function SetXAxisUnit($Unit = "") {
		$this->DataDescription ["Unit"] ["X"] = $Unit;
	}
	
	public function SetYAxisUnit($Unit = "") {
		$this->DataDescription ["Unit"] ["Y"] = $Unit;
	}
	
	/**
	 * Alias for SetSerieSymbol
	 * @param string $Name
	 * @param string $Symbol
	 */
	public function SetSeriesSymbol($Name, $Symbol) {
		$this->SetSeriesSymbol($Name, $Symbol);
	}
	
	public function SetSerieSymbol($Name, $Symbol) {
		$this->DataDescription ["Symbol"] [$Name] = $Symbol;
	}
	
	/**
	 * @todo should this be renamed to match camel case of all the rest?
	 * @param unknown_type $SerieName
	 */
	public function removeSerieName($SerieName) {
		if (isset ( $this->DataDescription ["Description"] [$SerieName] ))
			unset ( $this->DataDescription ["Description"] [$SerieName] );
	}
	
	/**
	 * @todo another camel case issue
	 */
	public function removeAllSeries() {
		foreach ( $this->DataDescription ["Values"] as $Key => $Value )
			unset ( $this->DataDescription ["Values"] [$Key] );
	}
	
	public function GetData() {
		return ($this->Data);
	}
	
	public function GetDataDescription() {
		return ($this->DataDescription);
	}
}