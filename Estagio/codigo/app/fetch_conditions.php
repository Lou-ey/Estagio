<?php
include 'config.php';

$metricColumnMapping = array(
    'Temperatura' => 'temperature',
    'Humidade' => 'humidity',
    'Ruído' => 'noise',
    'Qualidade do ar' => 'air_quality'
);

function getLatestSensorDataForMetric($metric)
{
    global $conn, $metricColumnMapping;

    $latestSensorData = array();

    if (array_key_exists($metric, $metricColumnMapping)) {
        $column = $metricColumnMapping[$metric];

        $sql = "SELECT * FROM sensordata WHERE (sensor_id, timestamp) IN (SELECT sensor_id, MAX(timestamp) AS max_created_at FROM sensordata GROUP BY sensor_id) AND $column IS NOT NULL";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $latestSensorData[] = $row;
            }
        }
    }

    return $latestSensorData;
}

function getAlertConditions()
{
    global $conn;

    $alertConditions = array();

    $result = $conn->query("SELECT * FROM alertconditions");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $alertConditions[] = $row;
        }
    }

    return $alertConditions;
}

function checkConditionsAndAccumulateAlerts($sensorData, $alertConditions)
{
    global $metricColumnMapping;
    $alerts = array();

    foreach ($alertConditions as $condition) {
        $sensorType = $condition['type'];
        $mappedType = $metricColumnMapping[$sensorType];

        if (array_key_exists($mappedType, $sensorData) && !empty($sensorData[$mappedType])) {
            $sensorValue = $sensorData[$mappedType];
            $conditionOperator = $condition['condition_type'];
            $conditionValue = $condition['value'];
            $status = $condition['status'];

            if ($status === 'active') {
                $timestamp = $sensorData['timestamp'];

                switch ($conditionOperator) {
                    case '>':
                        if ($sensorValue > $conditionValue) {
                            $alerts[] = array(
                                'message' => "O sensor ultrapassou o limite definido para $sensorType.",
                                'timestamp' => $timestamp,
                                'sensorType' => $sensorType,
                                'sensorValue' => $sensorValue
                            );
                        }
                        break;
                    case '<':
                        if ($sensorValue < $conditionValue) {
                            $alerts[] = array(
                                'message' => "O sensor está abaixo do limite definido para $sensorType.",
                                'timestamp' => $timestamp,
                                'sensorType' => $sensorType,
                                'sensorValue' => $sensorValue
                            );
                        }
                        break;
                    case '=':
                        if ($sensorValue == $conditionValue) {
                            $alerts[] = array(
                                'message' => "O sensor atingiu o limite definido para $sensorType.",
                                'timestamp' => $timestamp,
                                'sensorType' => $sensorType,
                                'sensorValue' => $sensorValue
                            );
                        }
                        break;
                    case '>=':
                        if ($sensorValue >= $conditionValue) {
                            $alerts[] = array(
                                'message' => "O sensor ultrapassou ou atingiu o limite definido para $sensorType.",
                                'timestamp' => $timestamp,
                                'sensorType' => $sensorType,
                                'sensorValue' => $sensorValue
                            );
                        }
                        break;
                    case '<=':
                        if ($sensorValue <= $conditionValue) {
                            $alerts[] = array(
                                'message' => "O sensor está abaixo ou atingiu o limite definido para $sensorType.",
                                'timestamp' => $timestamp,
                                'sensorType' => $sensorType,
                                'sensorValue' => $sensorValue
                            );
                        }
                        break;
                    default:
                        break;
                }
            }
        }
    }

    return $alerts;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    $alertConditions = getAlertConditions();

    $allAlerts = array(); // Acumula todos os alertas

    if (!empty($alertConditions)) {
        foreach ($alertConditions as $condition) {
            $metric = $condition['type'];
            $latestSensorData = getLatestSensorDataForMetric($metric);

            $alertMessages = array(); // Acumula alertas para esta condição

            foreach ($latestSensorData as $sensorData) {
                $alerts = checkConditionsAndAccumulateAlerts($sensorData, array($condition));
                $alertMessages = array_merge($alertMessages, $alerts);
            }

            $allAlerts = array_merge($allAlerts, $alertMessages);
        }

        // Guardar alertas na sessão
        if (!isset($_SESSION['alerts'])) {
            $_SESSION['alerts'] = array();
        }

        foreach ($allAlerts as $alert) {
            $_SESSION['alerts'][] = $alert;
        }

        header('Content-Type: application/json');
        echo json_encode($allAlerts);
    } else {
        echo json_encode([]);
    }
}
?>
