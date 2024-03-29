<?php
/************************************************************************/
/* This version name NPDS Copyright (c) 2001-2019 by Philippe Brunier   */
/* ===========================                                          */
/*                                                                      */
/* UPLOAD Language File                                                 */
/*                                                                      */
/************************************************************************/

function upload_translate($phrase) {
 switch($phrase) {
   /////// fichier
   case "Pièces jointes": $tmp="Attachments"; break;
   case "Fichier": $tmp="Datei"; break;
   case "Type": $tmp="Typ"; break;
   case "Taille": $tmp="Größe"; break;
   case "Affichage intégré :": $tmp="Integriertes Display :"; break;
   case "Oui": $tmp="Ja"; break;
   case "Non": $tmp="Nein"; break;
   case "Supprimer les fichiers sélectionnés": $tmp="Löschen ausgewählten Dateien"; break;
   case "Fichier joint": $tmp="Wählen Sie eine Datei anhängen :"; break;
   case "Joindre": $tmp="Datei senden"; break;
   case "Adapter": $tmp="Aktualisierung"; break;
   case "Visibilité": $tmp="Sichtbarkeit"; break;
   case "Total :": $tmp="Gesamt :"; break;
   case "Fichier non trouvé": $tmp="Datei nicht gefunden"; break;
   case "Fichier non visible": $tmp="Datei nicht sichtbar"; break;
   case "Télécharg.": $tmp="Download(s)"; break;
   case "Prévisualisation :": $tmp="Vorschau :"; break;
   case "Ces Images sont disponibles sur votre site": $tmp="Diese Bilder sind auf Ihre Website."; break;
   case "Ces Documents sont disponibles sur votre site": $tmp="Diese Bilder sind auf Ihre Website."; break;
   case "Ces Images et ces Documents sont rattachés à votre compte.": $tmp="Ihre Bilder und Dokumente."; break;
   case "Télécharger un fichier sur le serveur"; $tmp="Datei-Upload"; break;
   case 'Extensions autorisées'; $tmp='Erlaubte Dateierweiterungen'; break;
   /////// javascript
   case "Supprimer les fichiers sélectionnés ?": $tmp="Ausgewählten Dateien löschen?"; break;
   case "Cette page a déjà été envoyée, veuillez patienter": $tmp="Diese Seite wurde versandt, bitte haben Sie Geduld"; break;
   case "Vous devez tout d'abord choisir la Pièce jointe à supprimer": $tmp="Sie müssen die Anlage, die Sie löschen möchten"; break;
   case "Vous devez sélectionner un fichier": $tmp="Sie müssen eine Datei auswählen"; break;
   case "Joindre le fichier maintenant ?": $tmp="Datei senden jetzt?"; break;
   case "Rafraîchir la page": $tmp="Laden Sie die Seite"; break;
   case "Modèles": $tmp="Vorlagen"; break;
   case "Installer": $tmp="Installieren"; break;
   case "Etes vous certains de vouloir installer le thème": $tmp="Sind Sie sicher, dass Sie die Vorlage installieren"; break;
   /////// class upload
   case "La taille de ce fichier excède la taille maximum autorisée": $tmp="Die Dateigröße ¸berschreitet die maximale Dateigröße"; break;
   case "Ce type de fichier n'est pas autorisé": $tmp="Diese typ von Datei ist nicht autorisierte"; break;
   case "Le code erreur est : %s": $tmp="Fehlercode war : %s"; break;
   case "Attention": $tmp="Warnung"; break;
   case "Session terminée.": $tmp="Session gestoppt."; break;
   case "Erreur de téléchargement du fichier %s (%s) - Le fichier n'a pas été sauvé": $tmp="Fehler beim Hochladen der Datei %s (%s) - Datei nicht gespeichert"; break;
   case "Fichier {NAME} bien reçu ({SIZE} octets transférés)": $tmp="datei {NAME} hochgeladen ({SIZE} ¸bertragenen Bytes)"; break;
   case "Erreur de téléchargement du fichier - fichier non sauvegardé.": $tmp="Fehler beim Hochladen der Datei - Datei nicht gespeichert"; break;

   default: $tmp = "Es gibt keine übersetzung [** $phrase **]"; break;
 }
   return (htmlentities($tmp,ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401,'utf-8'));
}
?>