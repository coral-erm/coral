# Get Resource, check if currentStartDate and currentEndDate are NULL or 0000-00-00
# If not, then check ResourceAcquisition subscriptionStartDate and subscriptionEndDate;
# if they are identical to each other, replace them with the data from Resource
# If ResourceAcquisition subscriptionStartDate and subscriptionEndDate are different from each other,
# assume they've intentionally been updated and leave them alone.

UPDATE ResourceAcquisition RA
JOIN Resource R ON R.resourceID = RA.resourceID
SET
RA.subscriptionStartDate=R.currentStartDate,
RA.subscriptionEndDate=R.currentEndDate
WHERE
(((R.currentStartDate IS NOT NULL) AND (R.currentStartDate <> "0000-00-00"))
  OR ((R.currentEndDate IS NOT NULL) AND (R.currentEndDate <> "0000-00-00")))
AND (R.currentStartDate != R.currentEndDate
  OR (R.currentStartDate IS NULL OR R.currentEndDate IS NULL))
AND RA.subscriptionStartDate = RA.subscriptionEndDate;

