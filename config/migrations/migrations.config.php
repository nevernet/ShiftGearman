<?php
return array(
    'ShiftDoctrine' => array(
        'orm' => array(
            'entityManagers' => array(
                'writer' => array(
                    'metadataDrivers' => array(
                        0 => array('mappingDirsAutodiscover' => array('ShiftGearman'))
                    ),
                ),
                'reader' => array(
                    'metadataDrivers' => array(
                        0 => array('mappingDirsAutodiscover' => array('ShiftGearman'))
                    ),
                ),
            )
        )
    )
);

